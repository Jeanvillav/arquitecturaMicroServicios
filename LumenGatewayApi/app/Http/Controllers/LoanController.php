<?php

namespace App\Http\Controllers;

use App\Services\BookService;
use App\Services\LoanService;
use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LoanController extends Controller
{
    use ApiResponser;

    /**
     * The service to consume the loan service
     * @var LoanService
     */
    public $loanService;

    /**
     * The service to consume the book service
     * @var BookService
     */
    public $bookService;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(LoanService $loanService, BookService $bookService)
    {
        $this->loanService = $loanService;
        $this->bookService = $bookService;
    }

    /**
     * Retrieve and show all the loans
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        return $this->successResponse($this->loanService->obtainLoans());
    }

    /**
     * Creates an instance of loan
     * @return Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // specific validation for book_id
        if ($request->has('book_id')) {
            $this->bookService->obtainBook($request->book_id);
        }

        return $this->successResponse($this->loanService->createLoan($request->all()), Response::HTTP_CREATED);
    }

    /**
     * Obtain and show an instance of loan
     * @return Illuminate\Http\Response
     */
    public function show($loan)
    {
        return $this->successResponse($this->loanService->obtainLoan($loan));
    }

    /**
     * Updated an instance of loan
     * @return Illuminate\Http\Response
     */
    public function update(Request $request, $loan)
    {
        if ($request->has('book_id')) {
            $this->bookService->obtainBook($request->book_id);
        }

        return $this->successResponse($this->loanService->editLoan($request->all(), $loan));
    }

    /**
     * Removes an instance of loan
     * @return Illuminate\Http\Response
     */
    public function destroy($loan)
    {
        return $this->successResponse($this->loanService->deleteLoan($loan));
    }

    /**
     * Retrieve overdue loans
     * @return Illuminate\Http\Response
     */
    public function overdue()
    {
        return $this->successResponse($this->loanService->obtainOverdueLoans());
    }
}
