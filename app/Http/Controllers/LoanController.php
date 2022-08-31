<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $user_id =  auth()->user()->id;
        $loans = Loan::where(["user_id" => $user_id])->get();
        $response = [];
        if ($loans->count() > 0) {
            foreach ($loans as $loan) {
                if ($loan->status == 2) {
                    $status = "Approved";
                } else  if ($loan->status == 3) {
                    $status = "Paid";
                } else {
                    $status = "Pending";
                }
                $response[] = [
                    'loan_amount' => $loan->loan_amount,
                    'loan_term' => $loan->loan_term,
                    'status' => $status
                ];
            }

            return response()->json([
                'success' => 'true',
                'message' => $response,
            ], 200);
        } else {
            return response()->json([
                'success' => 'false',
                'message' => 'No Loan Details Found.',
            ], 200);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'loan_amount' => 'required|numeric|min:1',
            'loan_term' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 'false',
                'errors' => $validator->errors(),
            ]);
        }

        $user_id =  auth()->user()->id;
        $loan = Loan::create([
            'user_id' => $user_id,
            'loan_amount' => $request->loan_amount,
            'loan_term' => $request->loan_term
        ]);

        return response()->json(
            [
                'success' => 'true',
                'message' => ['loan_id' => $loan->id],
            ],
            200
        );
    }
}
