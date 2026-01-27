<?php

namespace App\Http\Controllers;

use App\Models\Document as ModelsDocument;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    public function index(){
        $document = ModelsDocument::all();

        return response()->json([
            'document' => $document
        ]);
    }
}
