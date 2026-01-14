<?php

namespace App\Services;

use App\Traits\ConsumesExternalService;

class LoanService
{
    use ConsumesExternalService;

    /**
     * The base uri to be used to consume the loans service
     * @var string
     */
    public $baseUri;

    /**
     * The secret to be used to consume the loans service
     * @var string
     */
    public $secret;

    public function __construct()
    {
        $this->baseUri = config('services.loans.base_uri');
        $this->secret = config('services.loans.secret');
    }

    /**
     * Get the full list of loans from the loans service
     * @return string
     */
    public function obtainLoans()
    {
        return $this->performRequest('GET', '/loans');
    }

    /**
     * Create an instance of loan using the loans service
     * @return string
     */
    public function createLoan($data)
    {
        return $this->performRequest('POST', '/loans', $data);
    }

    /**
     * Get a single loan from the loans service
     * @return string
     */
    public function obtainLoan($loan)
    {
        return $this->performRequest('GET', "/loans/{$loan}");
    }

    /**
     * Edit a single loan from the loans service
     * @return string
     */
    public function editLoan($data, $loan)
    {
        return $this->performRequest('PUT', "/loans/{$loan}", $data);
    }

    /**
     * Remove a single loan from the loans service
     * @return string
     */
    public function deleteLoan($loan)
    {
        return $this->performRequest('DELETE', "/loans/{$loan}");
    }

    /**
     * Get overdue loans
     * @return string
     */
    public function obtainOverdueLoans()
    {
        return $this->performRequest('GET', '/loans/overdue');
    }

    /**
     * Get loans by user
     * @return string
     */
    public function obtainLoansByUser($user)
    {
        return $this->performRequest('GET', "/loans/user/{$user}");
    }
}
