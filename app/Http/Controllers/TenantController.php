<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tenant;

class TenantController extends Controller
{
	public function __construct() {
		$this->middleware('auth');
		if(auth()->id() != 1) redirect('/');
	}  

	/**
	 * Show tenants of user
	 */
	public function index() {
		
		return view('tenants.index')->with([
			'tenants' => $this->getTenants()
		]);
	}  

	/**
	 * Enter tenant
	 */
	public function enter($id) {

		// login 
	}  

	/**
	 * Create tenant 
	 */
	public function create() {
		return view('tenants.create');
	}  

	/**
	 * Edit tenant 
	 */
	public function edit($id) {
		// check owner of tenant

		//return if owner
		return view('tenants.edit')->with([
			'id' => $id
 		]);
	}  

	/**
	 * Save tenant 
	 */
	public function save(Request $request) {
		//check if exists 

		// save if not
		return redirect('/projects')->with([
			'message' => 'The tenant was created',
			'tenants' => $this->getTenants()
		]);
	} 

	/**
	 * Update tenant 
	 */
	public function update(Request $request) {
		return redirect('/projects')->with([
			'message' => 'The tenant was updated',
			'tenants' => $this->getTenants()
		]);
	}  


	protected function getTenants() {
		$tenants = Tenant::where('global_id', auth()->id())->get();

		return $tenants;
	}
	


}