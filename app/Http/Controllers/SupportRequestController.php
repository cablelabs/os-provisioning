<?php

namespace App\Http\Controllers;

class SupportRequestController extends BaseController
{
    /**
     * defines the formular fields for the edit and create view
     */
    public function view_form_fields($model = null)
    {
        $categories = [
            'Network Outage' => 'Network Outage',
            'Service Problems' => 'Service Problems',
            'General Questions' => 'General Questions',
            'CMTS or CM Issues' => 'CMTS or CM Issues',
            ];
        $priorities = [
            'Critical' => 'Critical',
            'Major' => 'Major',
            'Minor' => 'Minor',
            ];

        // label has to be the same like column in sql table
        return [
            ['form_type' => 'select', 'name' => 'category', 'description' => 'Category', 'value' => $categories],
            ['form_type' => 'select', 'name' => 'priority', 'description' => 'Priority', 'value' => $priorities],
            ['form_type' => 'text', 'name' => 'mail', 'description' => 'Mail'],
            ['form_type' => 'text', 'name' => 'phone', 'description' => 'Phone'],
            ['form_type' => 'textarea', 'name' => 'text', 'description' => 'Description'],
            ];
    }

    public function index()
    {
        $headline = BaseViewController::translate_view('SupportRequest', 'Header');
        $view_header = BaseViewController::translate_view('Overview', 'Header');

        $sla = \App\Sla::first();

        if ($sla->valid()) {
            // show support request formular
            return \Redirect::route('SupportRequest.create');
        } else {
            // show SLA request with response time table, confluence link and Button to skip SLA agreement and continue with request formular
            return \View::make('SupportRequest.index', $this->compact_prep_view(compact('headline', 'view_header', 'model', 'create_allowed', 'delete_allowed')));
        }
    }
}
