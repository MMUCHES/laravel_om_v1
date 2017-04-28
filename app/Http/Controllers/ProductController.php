<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductDetail;
use App\ProductExplain;
use App\Product;
use App\User;
use File;

class ProductController extends Controller
{
    public function __construct()
    {
    	$this->middleware('auth:api')
    		->except(['index', 'show']);
    }

    public function index()
    {
    	$products = Product::orderBy('created_at', 'desc')
    		->get(['id', 'name', 'image']);

    	return response()
    		->json([
    			'products' => $products
    		]);
    }

    public function create()
    {
        $form = Product::form();
    	return response()
    		->json([
    			'form' => $form
    		]);
    }

    public function store(Request $request)
    {
    	$this->validate($request, [
    		'name' => 'required|max:255',
    		'description' => 'required|max:3000',
    		'image' => 'required|image',
    		'details' => 'required|array|min:1',
    		'details.*.name' => 'required|max:255',
    		'details.*.qty' => 'required|max:255',
    		'explains' => 'required|array|min:1',
    		'explains.*.description' => 'required|max:3000'
    	]);

    	$details = [];

        foreach($request->details as $detail) {
            $details[] = new ProductDetail($detail);
        }

	    $explains = [];

        foreach($request->explains as $explain) {
            $explains[] = new ProductExplain($explain);
        }

    	if(!$request->hasFile('image') && !$request->file('image')->isValid()) {
    		return abort(404, 'Image not uploaded!');
    	}

    	$filename = $this->getFileName($request->image);
    	$request->image->move(base_path('public/images'), $filename);

    	$product = new Product($request->only('name', 'description'));
    	$product->image = $filename;

    	$request->user()->products()
    		->save($product);

    	$product->details()
    		->saveMany($details);

    	$product->explains()
    		->saveMany($explains);

    	return response()
    	    ->json([
    	        'saved' => true,
    	        'id' => $product->id,
                'message' => 'You have successfully created product!'
    	    ]);
    }

    private function getFileName($file)
    {
    	return str_random(32).'.'.$file->extension();
    }

     public function show($id)
    {
        $product = Product::with(['user', 'details', 'explains'])
            ->findOrFail($id);

        return response()
            ->json([
                'product' => $product
            ]);
    }

 public function edit($id, Request $request)
    {
        $form = $request->user()->products()
            ->with(['details' => function($query) {
                $query->get(['id', 'name', 'qty']);
            }, 'explains' => function($query) {
                $query->get(['id', 'description']);
            }])
            ->findOrFail($id, [
                'id', 'name', 'description', 'image'
            ]);

        return response()
            ->json([
                'form' => $form
            ]);
    }

    public function update($id, Request $request)
    {
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required|max:255',
            'description' => 'required|max:3000',
            'image' => 'image',
            'details' => 'required|array|min:1',
            'details.*.id' => 'integer|exists:product_details',
            'details.*.name' => 'required|max:255',
            'details.*.qty' => 'required|max:255',
            'explains' => 'required|array|min:1',
            'explains.*.description' => 'required|max:3000'
        ]);

        $product = $request->user()->products()
            ->findOrFail($id);

        $details = [];
        $detailsUpdated = [];

        foreach($request->details as $detail) {
            if(isset($detail['id'])) {
                ProductDetail::where('product_id', $product->id)
                    ->where('id', $detail['id'])
                    ->update($detail);

                $detailsUpdated[] = $detail['id'];
            } else {
                $details[] = new ProductDetail($detail);
            }
        }

        $explains = [];
        $explainsUpdated = [];

        foreach($request->explains as $explain) {
            if(isset($explains['id'])) {
                ProductExplain::where('product_id', $product->id)
                    ->where('id', $explain['id'])
                    ->update($explain);

                $explainsUpdated[] = $explain['id'];
            } else {
                $explains[] = new ProductExplain($explain);
            }

        }

        $product->name = $request->name;
        $product->description = $request->description;

        // upload image
        if ($request->hasfile('image') && $request->file('image')->isValid()) {
            $filename = $this->getFileName($request->image);
            $request->image->move(base_path('/public/images'), $filename);

            // remove old image
            File::delete(base_path('/public/images/'.$product->image));
            $product->image = $filename;
        }

        $product->save();

        ProductDetail::whereNotIn('id', $detailsUpdated)
            ->where('product_id', $product->id)
            ->delete();

        ProductExplain::whereNotIn('id', $explainsUpdated)
            ->where('product_id', $product->id)
            ->delete();

        if(count($details)) {
            $product->details()->saveMany($details);
        }

        if(count($explains)) {
            $product->explains()->saveMany($explains);
        }

        return response()
            ->json([
                'saved' => true,
                'id' => $product->id,
                'message' => 'You have successfully updated product!'
            ]);
    }
    public function destroy($id, Request $request)
    {
        $product = $request->user()->products()
            ->findOrFail($id);

        ProductDetail::where('product_id', $product->id)
            ->delete();

        ProductExplain::where('product_id', $product->id)
            ->delete();

        // remove image
        File::delete(base_path('/public/images/'.$product->image));

        $product->delete();

        return response()
            ->json([
                'deleted' => true
            ]);
    }
}
