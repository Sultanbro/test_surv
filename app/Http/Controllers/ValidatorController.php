<?php

namespace App\Http\Controllers;

use App\Console\Commands\ValidatorCheck;
use App\Validator\ValidatorFiles;
use App\Validator\ValidatorNumbers;
use Carbon\Carbon;
use Illuminate\Http\Request;

use Auth;
use App\User;
use DB;
use Illuminate\Support\Facades\View;
use Maatwebsite\Excel\Facades\Excel;
use Session;


class ValidatorController extends Controller {

	public function __construct() {
		View::share( 'menu', 'validator' );
		View::share( 'title', 'Валидатор номеров' );
		//$this->middleware('auth');
	}


	public function index( Request $request ) {

		$user = User::bitrixUser();
		$uid  = $user->ID;

		if ( isset( $request['start'] ) && $file = ValidatorFiles::where( 'id', (int) $request['start'] )->where( 'status', ValidatorFiles::IN_MODERATION )->first() ) {
			$file->status = ValidatorFiles::IN_PROGRESS;
			$file->save();
		}

		$files = ValidatorFiles::orderBy( 'created_at', 'desc' )->where( 'user_id', $uid )->get();

		$number_count         = [];
		$number_count_checked = [];
		foreach ( $files as $file ) {
			$count                             = ValidatorNumbers::orderBy( 'updated_at', 'desc' )->where( 'file_id', $file->id )->count();
			$number_count[ $file->id ]         = $count;
			$count_checked                     = ValidatorNumbers::orderBy( 'updated_at', 'desc' )
			                                                     ->where( 'file_id', $file->id )
			                                                     ->where( 'is_alive', '!=', ValidatorNumbers::NONE )
			                                                     ->where( 'is_alive', '!=', ValidatorNumbers::WAIT )->count();
			$number_count_checked[ $file->id ] = $count_checked;
		}


		return view( 'validator.index' )->with( 'files', $files )
		                                ->with( 'number_count', $number_count )
		                                ->with( 'number_count_checked', $number_count_checked );

	}

	public function process( Request $request ) {

		$user = User::bitrixUser();
		$uid  = $user->ID;


		if ( $request->hasFile( 'file' ) && $request->file( 'file' )->isValid() ) {
			$file = $request->file( 'file' );

			$file_name = $uid . '_' . time() . '.' . $file->getClientOriginalExtension();

			$file->move( "static/validator", $file_name );

			$file_path = public_path() . '/static/validator/' . $file_name;

			$array_excel   = [];
			$highestColumn = 0;
			Excel::load( $file_path, function ( $reader ) use ( & $array_excel, &$highestColumn ) {
				$objExcel      = $reader->getExcel();
				$sheet         = $objExcel->getSheet( 0 );
				$highestColumn = $sheet->getHighestColumn();
				$array_excel   = $sheet->rangeToArray( 'A1:' . $highestColumn . '10' );
			} );

			return view( 'validator.process' )
				->with( 'excel', $array_excel )
				->with( 'highestColumn', $highestColumn )
				->with( 'file_title', $file->getClientOriginalName() )
				->with( 'file_name', $file_name );
		} else {
			$file_id = ValidatorFiles::create( [
				'user_id' => $uid,
				'title'   => $request['file_title'],
				'file'    => $request['file_name'],
				'status'  => ValidatorFiles::IN_MODERATION,
			] )->id;

			$file_path = public_path() . '/static/validator/' . $request['file_name'];

			$column_types = $request['column_type'];

			$name_columns   = [];
			$number_columns = [];
			foreach ( $column_types as $column_type ) {
				if ( ! empty( $column_type ) ) {
					$arr = explode( '_', $column_type );
					if ( $arr[0] == 'name' ) {
						$name_columns[] = $arr[1];
					} else {
						$number_columns[] = $arr[1];
					}
				}
			}

			$insert_data = [];

			Excel::load( $file_path, function ( $reader ) use ( $name_columns, $number_columns, $file_id, &$insert_data ) {
				$objExcel   = $reader->getExcel();
				$sheet      = $objExcel->getSheet( 0 );
				$highestRow = $sheet->getHighestRow();


				for ( $row = 1; $row <= $highestRow; $row ++ ) {

					$name = '';
					foreach ( $name_columns as $name_column ) {
						$name .= $sheet->getCell( $name_column . $row )->getValue() . ' ';

					}

					foreach ( $number_columns as $number_column ) {
						$cell   = $number_column . $row;
						$number = $sheet->getCell( $cell )->getValue();

						$number = preg_replace( '/[^0-9]/', '', $number );
						$number = substr( trim( $number ), - 10 );

						if ( ! empty( $number ) && strlen( $number ) == 10 ) {
							$insert_data[] = [
								'file_id'    => $file_id,
								'name'       => $name,
								'number'     => '7' . $number,
								'cell'       => $cell,
								'is_alive'   => ValidatorNumbers::NONE,
								'created_at' => Carbon::now(),
								'updated_at' => Carbon::now(),
							];
						}
					}
				}
			} );

			ValidatorNumbers::insert( $insert_data );
		}

		return redirect( '/validator/index/' );
	}


	public function numbers( Request $request, $file_id ) {

		$user = User::bitrixUser();
		$uid  = $user->ID;
		$file = ValidatorFiles::where( 'id', $file_id )->where( 'user_id', $uid )->first();
		if ( empty( $file ) ) {
			return redirect( '/validator/index/' );
		}


		$numbers = ValidatorNumbers::orderBy( 'updated_at', 'desc' )->where( 'file_id', $file_id )->get();

		return view( 'validator.numbers' )->with( 'numbers', $numbers );

	}


	//vendor/phpoffice/phpexcel/Classes/PHPExcel/Shared/File.php  для работы этой функции все realpath заменили  на public_path
	public function download( Request $request, $file_id, $type ) {
		$user = User::bitrixUser();
		$uid  = $user->ID;

		$status = [
			'active'       => ValidatorNumbers::ALIVE,
			'disconnected' => ValidatorNumbers::DISCONNECTED,
			'notactive'    => ValidatorNumbers::DIED,
		];

		$file = ValidatorFiles::where( 'id', $file_id )->where( 'user_id', $uid )->first();
		if ( empty( $file ) || ! isset( $status[ $type ] ) ) {
			return redirect( '/validator/index/' );
		}

		$type = $status[ $type ];

		$file_path = public_path() . '/static/validator/' . $file->file;
		Excel::load( $file_path, function ( $reader ) use ( $file_id, &$insert_data, $type) {
			$objExcel = $reader->getExcel();
			$sheet    = $objExcel->getSheet( 0 );


			$numbers = ValidatorNumbers::orderBy( 'updated_at', 'desc' )->where( 'file_id', $file_id )->get();

			foreach ( $numbers as $number ) {
				$value = '';
				if ( $number->is_alive == $type ) {
					$value = '8' . substr( trim( $number->number ), - 10 );
				}
				$sheet->setCellValue( $number->cell, $value );
			}
		} )->setFilename( 'Отчет по статусы ' . $file_title = ValidatorNumbers::state($type))->export( 'xls' );;
	}
}

