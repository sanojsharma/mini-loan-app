<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Loan;
use App\Models\LoanRepayment;
use Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\LoanStatusEmail;

class AdminController extends Controller
{
    public function approveLoanRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric',
            'loan_id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors(),
            ]);
        }

        $loan = Loan::where(['user_id' => $request->user_id,'id'=>$request->loan_id,'status' => 0])->first();

        if ($loan) {
            /* Mark Loan as approved */
            $loanRequest = Loan::find($loan->id);
            $loanRequest->status = 1;
            $loanRequest->save();

            # Create loan Installtments on approval
            $loan_term = $loan->loan_term;
            $loan_amount = $loan->loan_amount;
            $installment_amount = round($loan_amount / $loan_term, 2);
            $remainder = round($loan_amount - ($installment_amount * $loan_term), 2);

            # Required to equalize the SUM (Consider 10000/3 last chunk should required extra 0.1)
            $last_installment = $installment_amount + $remainder;

            $approval_date = date('Y-m-d');
            $installment_data =  [];
            $next_scheduled_date =  date('Y-m-d', strtotime($approval_date . "+7 days"));

            for ($i = 1; $i <= ($loan_term - 1); $i++) {
                $installment_data[] = [
                    'loan_id' => $loan->id,
                    'user_id' => $request->user_id,
                    'loan_amount' => $installment_amount,
                    'payment_date' =>  $next_scheduled_date
                ];
                $next_scheduled_date =  date('Y-m-d', strtotime($next_scheduled_date . "+7 days"));
            }

            $last_date = date('Y-m-d', strtotime($next_scheduled_date . "+7 days"));
            $installment_data[] = [
                'loan_id' => $loan->id,
                'user_id' => $request->user_id,
                'loan_amount' => $last_installment,
                'payment_date' =>  $last_date
            ];

            $loanRepayment = LoanRepayment::insert($installment_data);
            if ($loanRepayment) {
                Mail::to("test@gmail.com")->send(new LoanStatusEmail()); # Notify via Email
                return response()->json([
                    'success' => 'true',
                    'message' => "Loan has been approved successfully",
                ]);
            } else {
                return response()->json([
                    'success' => 'false',
                    'message' => "Error in approving loan",
                ]);
            }
        } else {
            return response()->json([
                'success' => 'false',
                'errors' => "Loan details not found Or has been Approved",
            ]);
        }
    }

}
