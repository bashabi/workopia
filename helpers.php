<?php

/**
 * Get the base path
 * 
 * @param string $path
 * @return string
 * 
 */

function basePath($path = '')
{
    return __DIR__ . '/' . $path;               // __DIR__ - returns the absolute path
}

/**
 * Load a view
 * 
 * @param string $name
 * @return void
 * 
 */

function loadView($name, $data = [])
{

    //require basePath("views/{$name}.view.php");   //this function calls the basePath function above and simplifies the path. 

    $viewPath = basePath("App/views/{$name}.view.php");

    //to handle the error incase file doesnot exist
    if (file_exists($viewPath)) {
        extract($data);
        require $viewPath;
    } else {
        echo 'View ' . $name . ' does not exist';
    }
}


/**
 * Load a partials
 * 
 * @param string $name
 * @return void
 * 
 */

function loadPartials($name)
{
    //require basePath("views/partials/{$name}.php");

    $partialPath = basePath("App/views/partials/{$name}.php");

    if (file_exists($partialPath)) {

        require $partialPath;
    } else {
        echo 'View ' . $name . ' does not exist';
    }
}


/**
 * 
 * Inspect value(s)
 * 
 * @param mixed $value
 * @return void
 * 
 */

function inspect($value)
{

    echo '<pre>';
    var_dump($value);
    echo '</pre>';
}


/**
 * 
 * Inspect value(s) and die
 * 
 * @param mixed $value
 * @return void
 * 
 */

function inspectAndDie($value)
{

    echo '<pre>';
    die(var_dump($value));
    echo '</pre>';
}

/**
 * 
 * Format Salary
 * 
 * @param string $salary
 * @return string Formatted salary
 * 
 */


function formatSalary($salary)
{

    return '$' . number_format(floatval($salary));
}

/**
 * Sanitize Data
 * 
 * @param string $dirty
 * @return string
 * 
 */

function sanitize($dirty)
{

    return filter_var(trim($dirty), FILTER_SANITIZE_SPECIAL_CHARS);
}


/**
 * Redirect to a given URL
 * 
 * @param string $url
 * @return void
 */

function redirect($url)
{
    header("Location: {$url}");
    exit;
}
