<?php

namespace App\Http\Controllers;

use App\Loan;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    use ApiResponser;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Return list of loans
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $loans = Loan::all();
        return $this->successResponse($loans);
    }

    /**
     * Create a new loan
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'user_id' => 'required|integer|min:1',
            'book_id' => 'required|integer|min:1',
            'library_id' => 'required|integer|min:1',
            'loan_date' => 'required|date',
            'due_date' => 'required|date|after:loan_date',
            'status' => 'required|in:active,returned,overdue',
        ];

        $this->validate($request, $rules);

        $loan = Loan::create($request->all());

        return $this->successResponse($loan, Response::HTTP_CREATED);
    }

    /**
     * Obtain and show a loan
     * @return Illuminate\Http\Response
     */
    public function show($loan)
    {
        $loan = Loan::findOrFail($loan);
        return $this->successResponse($loan);
    }

    /**
     * Update an existing loan
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $loan)
    {
        $rules = [
            'user_id' => 'integer|min:1',
            'book_id' => 'integer|min:1',
            'library_id' => 'integer|min:1',
            'loan_date' => 'date',
            'due_date' => 'date',
            'return_date' => 'nullable|date',
            'status' => 'in:active,returned,overdue',
        ];

        $this->validate($request, $rules);

        $loan = Loan::findOrFail($loan);

        $loan->fill($request->all());

        if ($loan->isClean()) {
            return $this->errorResponse('At least one value must change', Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $loan->save();

        return $this->successResponse($loan);
    }

    /**
     * Remove an existing loan
     * @return Illuminate\Http\Response
     */
    public function destroy($loan)
    {
        $loan = Loan::findOrFail($loan);
        $loan->delete();
        return $this->successResponse($loan);
    }

    /**
     * Return list of overdue loans
     * @return Illuminate\Http\Response
     */
    public function overdue()
    {
        // Assuming overdue means status is 'overdue' OR (status is 'active' AND due_date < now)
        // For simplicity based on description "Préstamos vencidos", we'll check the status or date
        // Let's implement active loans past due date as overdue.
        
        $loans = Loan::where('status', 'overdue')
                     ->orWhere(function($query) {
                         $query->where('status', 'active')
                               ->where('due_date', '<', date('Y-m-d H:i:s'));
                     })
                     ->get();
                     
        return $this->successResponse($loans);
    }
    
    /**
     * Return loans by user
     * @return Illuminate\Http\Response
     */
    public function byUser($user)
    {
        $loans = Loan::where('user_id', $user)->get();
        return $this->successResponse($loans);
    }
}
