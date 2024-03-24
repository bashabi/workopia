<?php

namespace App\Controllers;

use Framework\Database;
use Framework\Session;
use Framework\Validation;
use Framework\Authorization;

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
        $listings = $this->db->query('SELECT * FROM listings ORDER BY created_at DESC')->fetchAll();
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

        $newListingData['user_id'] = Session::get('user')['id'];

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

            Session::setFlashMessage('success_message', 'Listing Created Successfully');

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

        //Authorization - Check if the user own the listing
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to delete this listing');
            return redirect('/listings/' . $listing->id);
        }

        $this->db->query('DELETE FROM listings WHERE id = :id', $params);

        //set flash message
        Session::setFlashMessage('success_message', 'Listing Deleted Successfully');

        redirect('/listings');
    }


    /**
     * show the listing edit form
     * 
     * @params 
     * 
     */

    public function edit($params)
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

        //Authorization - Check if the user own the listing
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
            return redirect('/listings/' . $listing->id);
        }


        loadView('listings/edit', [
            'listing' => $listing
        ]);
    }

    /**
     * UPDATE a Listing
     * 
     * 
     * @param array $params
     * @return void
     * 
     */

    public function update($params)
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

        //Authorization - Check if the user own the listing
        if (!Authorization::isOwner($listing->user_id)) {
            Session::setFlashMessage('error_message', 'You are not authorized to update this listing');
            return redirect('/listings/' . $listing->id);
        }

        $allowedFields = ['title', 'description', 'salary', 'tags', 'company', 'address', 'city', 'state', 'phone', 'email', 'requirements', 'benefits'];

        $updatedValues = [];
        $updatedValues = array_intersect_key($_POST, array_flip($allowedFields));

        $updatedValues = array_map('sanitize', $updatedValues);

        $requiredFields = ['title', 'description', 'email', 'city'];

        $errors = [];

        foreach ($requiredFields as $field) {
            if (empty($updatedValues[$field]) || !Validation::string($updatedValues[$field])) {
                $errors[$field] = ucfirst($field) . ' is required';
            }
        }

        if (!empty($errors)) {
            loadView('listings/edit', [
                'listing' => $listing,
                'errors' => $errors

            ]);
            exit;
        } else {
            //submit to the database
            $updatedFields = [];
            foreach (array_keys($updatedValues) as $field) {
                $updatedFields[] = "{$field} = :{$field}";
            }

            $updatedFields = implode(', ', $updatedFields);

            $updateQuery = "UPDATE listings SET $updatedFields where id = :id";
            $updatedValues['id'] = $id;

            $this->db->query($updateQuery, $updatedValues);

            //set flash message
            Session::setFlashMessage('success_message', 'Listing updated successfully');

            redirect('/listings/' . $id);
        }
    }

    /**
     * Search listing by keywords &/ location
     * 
     * @return void
     */

    public function search()
    {
        $keywords = isset($_GET['keywords']) ? trim($_GET['keywords']) : '';
        $location = isset($_GET['location']) ? trim($_GET['location']) : '';

        $query = "SELECT * FROM listings WHERE (title LIKE :keywords OR description LIKE :keywords OR tags LIKE :keywords OR company LIKE :keywords) AND (city LIKE :location OR state LIKE :location)";

        $params = [
            'keywords' => "%{$keywords}%",
            'location' => "%{$location}%",
        ];

        $listings = $this->db->query($query, $params)->fetchAll();

        loadView('/listings/index', [
            'listings' => $listings,
            'keywords' => $keywords,
            'location'  => $location
        ]);
    }
}
