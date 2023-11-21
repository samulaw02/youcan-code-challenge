<?php

namespace App\Traits;

use Illuminate\Http\Request;

Trait ProductMixin {

    public function isSearchable(Request $request)
    {
       $searchGlobal = $request->input('search_global');
       $sortColumn = $request->input('sort_column');
       return ($searchGlobal !== null || $sortColumn !== null);
    }

}
