<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanRepayment;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanCompletedEmail;


class TransactionController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request)
    {
        $user_id =  auth()->user()->id;
        $validator = Validator::make($request->all(), [
            'loan_id' => 'required|numeric',
        ]);

        $repayments = LoanRepayment::where(['user_id'=>$user_id,'loan_id' => $request->loan_id, 'status' => 0])->get();

        $response = [];
        if ($repayments->count() > 0) {
            foreach ($repayments as $repayment) {
                $response[] = [
                    'id' => $repayment->id,
                    'loan_amount' => $repayment->loan_amount,
                    'payment_date' => $repayment->payment_date
                ];
            }

            return response()->json([
                'success' => 'true',
                'message' => $response,
            ], 200);
        } else {
            return response()->json([
                'success' => 'false',
                'message' => 'No Details Found.',
            ], 200);
        }
    }


    public function store(Request $request)
    {
        //
        $validator = Validator::make($request->all(), [
            'repayment_id' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors(),
            ]);
        }

        $user_id =  auth()->user()->id;
        #Check if Payment for this has been already done
        $repaymentCheck = LoanRepayment::where(['id' => $request->repayment_id,'user_id'=>$user_id, 'status' => 0])->first();

        if (!$repaymentCheck) {
            return response()->json(
                [
                    'success' => 'false',
                    'message' => "Invalid Id or payment has already been done",
                ],
                200
            );
        }

        $transaction = Transaction::create([
            'repayment_id' => $request->repayment_id,
            'amount_paid' => $request->amount,
            'transaction_date' => date("y-m-d"),
            'paid_by' => 'UPI',
            'status' => 1
        ]);

        if ($transaction) {
            $repayment = LoanRepayment::find($request->repayment_id);
            $repayment->status = 1;
            $repayment->transaction_id = $transaction->id;
            $repayment->updated_at = date("Y-m-d h:i:s");
            $repayment->save();

            # Check remaining payments and mark Loan Status accordingly.
            $this->reCalculateLaonStatus($repayment->loan_id);

            return response()->json(
                [
                    'success' => 'true',
                    'message' => "Payment success",
                ],
                200
            );
        }
    }

    public function reCalculateLaonStatus($loan_id)
    {
        $loan = LoanRepayment::where(['loan_id' => $loan_id, 'status' => 0])->get();
        if ($loan->count() == 0) {  #Mark Loan as Paid If all the payments have been done
            Mail::to("test@gmail.com")->send(new LoanCompletedEmail()); #Send Notification
            $loan_update = Loan::find($loan_id);
            $loan_update->status = 3;
            $loan_update->updated_at = date("Y-m-d h:i:s");
            $loan_update->save();
        }
    }
}
