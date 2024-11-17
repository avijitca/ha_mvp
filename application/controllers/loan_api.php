<?php
/**
 * Loan API Controller
 * 
 * This controller handles all loan-related API endpoints, allowing users to create new loans
 * with specified parameters like loan amount, interest rate, duration, and start date.
 * 
 * PHP Version: 7.4.33 (recommended)
 * 
 * @package     CodeIgniter
 * @subpackage  Controllers
 * @category    Controller API
 * @author      Avijit Chakravarty
 * @created     2024-11-10
 * @updated     2024-11-11
 * @version     1.0
 */

defined('BASEPATH') or exit('No direct script access allowed');
require APPPATH.'libraries/RestController.php';
require APPPATH.'libraries/Format.php';
use chriskacerguis\RestServer\RestController;

class Loan_api extends RestController {
    
    /**
     * Constructor to load the Loan API model.
     */
    public function __construct() {
        parent::__construct();
        $this->load->model('loan_api_model');
        $time_zone='America/New_York';
        if(function_exists('date_default_timezone_set')){
            date_default_timezone_set($time_zone);
        }
    }

    /**
     * Create a New Loan
     * 
     * HTTP Method: POST
     * Endpoint: /loan_api/create_loan
     * 
     * This method handles the creation of a new loan entry. It expects a JSON payload 
     * with fields such as user_id, loan_amount, interest_rate, duration_years and start_date.
     * The method performs input validation to ensure the integrity of data. 
     * 
     * Example Request Body:
     * {
     *   "user_id": 1,
     *   "loan_amount": 20000,
     *   "interest_rate": 15,
     *   "duration_years": 3,
     *   "start_date": "2024-11-05"
     * }
     * 
     * Responses:
     * - 201 Created: Loan created successfully.
     * - 400 Bad Request: Invalid input or missing required fields.
     * 
     * @return void
     */
    public function create_loan_post() {
        // Get the JSON input data
        $data = json_decode(file_get_contents("php://input"), true);
       
        // Check if the Data is Valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Required Fields
        $requiredFields = ['user_id', 'loan_amount', 'interest_rate', 'duration_years', 'start_date'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                $this->response(['message' => "Field '$field' is required"], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Validate Data Types and Formats
        if (!is_numeric($data['user_id']) || $data['user_id'] <= 0) {
            $this->response(['message' => 'Invalid user_id'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['loan_amount']) || $data['loan_amount'] <= 0) {
            $this->response(['message' => 'Invalid loan_amount'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['interest_rate']) || $data['interest_rate'] < 0 || $data['interest_rate'] > 100) {
            $this->response(['message' => 'Invalid interest_rate'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (!is_numeric($data['duration_years']) || $data['duration_years'] <= 0) {
            $this->response(['message' => 'Invalid duration_years'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Check if start_date is in valid 'YYYY-MM-DD' format
        $date_format = '/^\d{4}-\d{2}-\d{2}$/';
        if (!preg_match($date_format, $data['start_date'])) {
            $this->response(['message' => 'Invalid date format for start_date. Use YYYY-MM-DD'], RestController::HTTP_BAD_REQUEST);
            return;
        }        
        
        // Attempt to create the loan record
        $success = $this->loan_api_model->add_loan($data);
        
        // Return a response based on the success of the insertion
        if ($success) {
            $this->response(['message' => 'Loan created successfully'], RestController::HTTP_CREATED);
        } else {
            $this->response(['message' => 'Failed to create loan'], RestController::HTTP_BAD_REQUEST);
        }
    }
    /**
    * Update a Loan Record by ID
    * 
    * HTTP Method: PUT
    * Endpoint: /loan_api/update_loan/{id}
    * 
    * This method updates an existing loan record based on the provided ID. It expects a JSON payload 
    * with fields such as loan_amount, interest_rate, duration_years, start_date, and status.
    * The method performs input validation to ensure the integrity of data.
    * 
    * Example Request:
    * PUT /loan_api/update_loan/1
    * 
    * Example Request Body (JSON):
    * {
    *   "user_id": 1,
    *   "loan_amount": 25000,
    *   "interest_rate": 12,
    *   "duration_years": 2,
    *   "start_date": "2024-08-17",
    *   "status": "completed"
    * }
    * 
    * Responses:
    * - 200 OK: Loan updated successfully
    * - 400 Bad Request: Invalid input or missing required fields
    * - 404 Not Found: No loan found with the provided ID
    * 
    * @param int $id Loan ID passed as a URL segment
    * 
    * @return void
    */
    public function update_loan_put($id = null) {
        // Validate the ID parameter
        if ($id === null || !is_numeric($id) || $id <= 0) {
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        
        // Check if a record exist
        $record_exist= $this->loan_api_model->check_record_exist($id);
        if(empty($record_exist)){
            $this->response(['message' => 'Loan not found'], RestController::HTTP_NOT_FOUND);
            return;
        }
        // Get the raw JSON input data
        $data = json_decode(file_get_contents("php://input"), true);

        // Check if the Data is Valid JSON
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->response(['message' => 'Invalid JSON format'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Required Fields
        $allowedFields = ['user_id', 'loan_amount', 'interest_rate', 'duration_years', 'start_date', 'status'];
        foreach ($allowedFields as $field) {
            if (empty($data[$field])) {
                $this->response(['message' => "Field '$field' is required"], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }        
        
        $data['updated_at']=date('Y-m-d H:i:s');
        // Check if there's at least one field to update
        if (empty($data)) {
            $this->response(['message' => 'No valid fields provided to update'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        // Validate Data Types and Formats
        if (isset($data['loan_amount']) && (!is_numeric($data['loan_amount']) || $data['loan_amount'] <= 0)) {
            $this->response(['message' => 'Invalid loan_amount'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['interest_rate']) && (!is_numeric($data['interest_rate']) || $data['interest_rate'] < 0 || $data['interest_rate'] > 100)) {
            $this->response(['message' => 'Invalid interest_rate'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['duration_years']) && (!is_numeric($data['duration_years']) || $data['duration_years'] <= 0)) {
            $this->response(['message' => 'Invalid duration_years'], RestController::HTTP_BAD_REQUEST);
            return;
        }

        if (isset($data['start_date'])) {
            $date_format = '/^\d{4}-\d{2}-\d{2}$/';
            if (!preg_match($date_format, $data['start_date'])) {
                $this->response(['message' => 'Invalid date format for start_date. Use YYYY-MM-DD'], RestController::HTTP_BAD_REQUEST);
                return;
            }
        }

        // Update the loan record in the database using the model
        $success = $this->loan_api_model->update_loan($id, $data);

        // Check if the update was successful
        if ($success) {
            $this->response(['message' => 'Loan updated successfully'], RestController::HTTP_OK);
        } else {
            $this->response(['message' => 'Failed to update loan or loan not found'], RestController::HTTP_NOT_FOUND);
        }
    }

    /**
    * Delete a Loan Record by ID
    * 
    * HTTP Method: DELETE
    * Endpoint: /loan_api/delete_loan/{id}
    * 
    * This method deletes an existing loan record based on the provided ID.
    * 
    * Example Request:
    * DELETE /loan_api/delete_loan/1
    * 
    * Responses:
    * - 200 OK: Loan deleted successfully
    * - 400 Bad Request: Invalid loan ID
    * - 404 Not Found: No loan found with the provided ID
    * 
    * @param int $id Loan ID passed as a URL segment
    * 
    * @return void
    */
   public function delete_loan_delete($id = null) {
       // Validate the ID parameter
       if ($id === null || !is_numeric($id) || $id <= 0) {
           $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
           return;
       }
       // Check if a record exist
        $record_exist= $this->loan_api_model->check_record_exist($id);
        if(empty($record_exist)){
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_NOT_FOUND);
            return;
        }
       // Attempt to delete the loan using the model
       $this->loan_api_model->delete_loan($id);
       $this->response(['message' => 'Loan deleted successfully'], RestController::HTTP_OK);
    }
    
    /**
    * Get a List of All Loans
    * 
    * HTTP Method: GET
    * Endpoint: /loan_api/get_all_loans
    * 
    * This method retrieves a list of all loan records from the database. It returns loan details
    * such as loan amount, interest rate, duration, and start date.
    * 
    * Example Request:
    * GET /loan_api/get_all_loans
    * 
    * Responses:
    * - 200 OK: Returns a list of all loans
    * - 404 Not Found: No loans found in the database
    * 
    * @return void
    */
   public function get_all_loans_get() {
       // Retrieve all loans from the database using the model
       $loans = $this->loan_api_model->get_all_loans();

       // Check if any loans were retrieved
       if (!empty($loans)) {
           // Return the list of loans with a 200 OK status
           $this->response(['loans' => $loans, 'message' => 'Loans retrieved successfully'], RestController::HTTP_OK);
       } else {
           // Return a 404 Not Found status if no loans are found
           $this->response(['message' => 'No loans found'], RestController::HTTP_NOT_FOUND);
       }
   }
   /**
     * Retrieve a Loan Record by ID
     * 
     * HTTP Method: GET
     * Endpoint: /loan_api/view_loan/{id}
     * 
     * This method retrieves a specific loan record based on its ID. It expects an integer ID as a parameter.
     * The method validates the ID, checks if a loan exists with that ID, and returns the loan data if found.
     * 
     * Example Request:
     * GET /loan_api/view_loan/1
     * 
     * 
     * Responses:
     * - 200 OK: Returns the loan record with fields such as loan_amount, interest_rate, duration_years, etc.
     * - 400 Bad Request: If the ID is missing or invalid.
     * - 404 Not Found: If no loan is found with the given ID.
     * 
     * @param int $id Loan ID passed as a URL segment.
     * 
     * @return void
     */    
    public function view_loan_get($id=NULL){
        //  Validate the ID parameter
        if ($id === null || !is_numeric($id) || $id <= 0) {
            $this->response(['message' => 'Invalid or missing loan ID'], RestController::HTTP_BAD_REQUEST);
            return;
        }
        //  Fetch the loan record from the database using the model
        $loan = $this->loan_api_model->get_loan_by_id($id);
        
        // Check if the loan record exists
        if ($loan) {
            // Return the loan record with a 200 OK status
            $this->response($loan, RestController::HTTP_OK);
        } else {
            // Return a 404 Not Found status if no loan record is found
            $this->response(['message' => 'Loan not found'], RestController::HTTP_NOT_FOUND);
        }
    }

    
}





