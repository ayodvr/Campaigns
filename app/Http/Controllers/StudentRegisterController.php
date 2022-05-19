<?php

namespace App\Http\Controllers;

use App\Event\StudentCreated;
use Illuminate\Http\Request;
use Codexshaper\WooCommerce\Facades\Coupon;
use Codexshaper\WooCommerce\Facades\Product;
use App\Models\StudentRegister;


class StudentRegisterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {    
        $students = StudentRegister::orderBy('id','desc')->paginate(10);
        return view('student.index')->with('students', $students);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('student.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'firstname'   => 'required',
            'lastname'    => 'required',
            'school'      => 'required',
            'image'       => 'image|required|max:1999',
            'email'       => 'required',
            'id_card'     => 'nullable',
            'phone'       => 'required',
            'date'        => 'required',
            'handle'      => 'required',
            'location'    => 'required'
        ]);

        $data = [
            'firstname'     => $request->get('firstname'),
            'lastname'      => $request->get('lastname'),
            'school'        => $request->get('school'),
            'phone'         => $request->get('phone'),
            'date'          => $request->get('date'),
            'handle'        => $request->get('handle'),
            'location'      => $request->get('location'),
            'image'         => $request->get('image'),
            'email'         => $request->get('email'),
            'id_card'       => $request->get('id_card')
        ];

        if ($request->has(['image'])) {
            $name = time().$request->file('image')->getClientOriginalName();
            $destination = public_path().'/StudentImages';
            $path='/StudentImages/'.$name;
            $request->file('image')->move($destination, $name);
            $data['image'] = $path;
        }

        if ($request->has(['id_card'])) {
            $name = time().$request->file('id_card')->getClientOriginalName();
            $destination = public_path().'/StudentImages';
            $path='/StudentImages/'.$name;
            $request->file('id_card')->move($destination, $name);
            $data['id_card'] = $path;
        }

        $check_user = StudentRegister::where('email', $request->email)->exists();
    
        if($check_user){
            
            return back()->with('error', 'You have already registered!');
        }

        $Register = StudentRegister::create($data);

        if($Register){

            $random = random_int(1000, 9999);

            $Coupon = [
                'code' => 'DW' . $random,
                'discount_type' => 'percent',
                'amount' => '5',
                'individual_use' => true,
                'email_restrictions' => $Register->email,
                'description' => $Register->firstname,
                'exclude_sale_items' => true
            ];
            
            $coupon = Coupon::create($Coupon);

            event (new StudentCreated($coupon));
        }

        return view('student.thankyou')->with('success', 'Registration Successfull');

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
