<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Validation;

class ListingController
{

    protected $db;

    public function __construct()
    {
        $config = require basePath('config/db.php');
        $this->db = new Database($config);
    }


    public function index()
    {
        $listings = $this->db->query('SELECT * FROM listings')->fetchAll();
        loadView('listings/index', ['listings' => $listings]);
    }


    public function create()
    {
        loadView('listings/create');
    }

    public function show($params)
    {
        $id = $params['id'] ?? '';
        $params = [
            'id' => $id,
        ];

        $listing  = $this->db->query('SELECT * FROM listings where id = :id', $params)->fetch();
        // inspect($listing);

        if (!$listing) {
            ErrorController::notFound('Listing Not found');
            return;
        }

        loadView('listings/show', [
            'listing' => $listing
        ]);
    }

    /**
     * Store method
     * 
     */

    public function store()
    {
        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $newListingData = array_intersect_key($_POST, array_flip($allowedFields));

        $newListingData['user_id'] = 7;

        $newListingData = array_map('sanitize', $newListingData);


        $requiredFields = ['title', 'description', 'email', 'city'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($newListingData[$field]) || !Validation::string($newListingData[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/create', [
                'errors' => $errors,
                'listing' => $newListingData
            ]);
        } else {
            //submit data



            $fields = [];

            foreach ($newListingData as $field => $value) {
                $fields[] = $field;
            }

            $fields = implode(', ', $fields);
            //inspectAndDie($fields);

            $values = [];
            foreach ($newListingData as $field => $value) {
                //convert empty string ti null
                if ($value === '') {
                    $newListingData['$field'] = null;
                }

                $values[] = ':' . $field;
            }
            $values = implode(', ', $values);
            //inspectAndDie($values);


            $query = "INSERT INTO  listings ({$fields}) VALUES ($values)";
            $this->db->query($query, $newListingData);

            redirect('/listings');
        }
    }


    /**
     * DELETE a listing
     * 
     * @param array $params
     * @return void
     */


    public function destroy($params)
    {
        $id = $params['id'];

        $params = [
            'id' => $id
        ];

        $listing = $this->db->query('SELECT * FROM listings WHERE id = :id', $params)->fetch();

        if (!$listing) {
            ErrorController::notFound('Listing not found');
            return;
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        //set flash message
        $_SESSION['success_message'] = 'Listing Deleted Successfully';
        redirect('/listings');
    }
}
