<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class StudioController extends Controller
{
    public function create()
    {
       // dd('studio create');
        //$this->checkpermission('module-create');
        $sizes = \DB::table('photosizes')->where('company_id', Auth::user()->company_id)->get();
        
        return view('backend.studio.create', compact('sizes'));
    }
    public function photosizelist()
    {
    	$sizes = \DB::table('photosizes')->where('company_id', Auth::user()->company_id)->get();
      //dd('list',$sizes);
        return view('backend.studio.sizelist',compact('sizes'));
    }
    public function sizecreate()
    {
      
        return view('backend.studio.photosizecreate');
    }
    public function photosizestore(Request $request)
    {
         $input=\Request::all();
       // dd($input);
        $this->validate($request, [
            'size' => 'required',
            'status' => 'required',
        ]);
        //dd('esevai store');
       $message = DB::table('photosizes')->insert([
                'company_id' => Auth::user()->company_id,
                'size' => $request->size,
                'status' => $request->status,
                 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                
                ]);
       if ($message) {
            return redirect()->route('studio.size.sizelist')->with('success_message', 'successfully created ');
        } else {
            return redirect()->route('studio.size.create')->with('error_message', 'Failed To create');
        }
    }
    public function studiobillverify(Request $request)
    {
      $input = \Request::all();
     // dd($input);
      $name = $request->name;
      $mobile = $request->mobile;
      $address = $request->address;
      $email = $request->email;
      $deliverd_date = $request->deliverd_date;
      $pack_size = $request->pack_size;
      $size = $request->size;
      $qty = $request->qty;
      $amt = $request->amt;
      $counts= sizeof($pack_size);
      
     // $amt = $request->amt;
      for($i=0; $i < $counts; $i++){
        if($pack_size[$i] != null && $size[$i] == null){
            $sizes[] = $pack_size[$i];
        }else{
            $sizes[] = $size[$i];
        }
      }

      $grand=0;
      for($i=0; $i < $counts; $i++){
        $price =$amt[$i];
        $grand += $price;
      }
      
     // dd($service_name,$service_id);
       // dd($input,$address, $mobile,$service_id,$amt);
        return view('backend.studio.studioverify', compact('grand','name','mobile','address','email','deliverd_date','sizes','qty','amt'));
    }
    public function studiobillstore(Request $request)
    {
        $input = \Request::all();
       
       $type='studio';
        // Insert Customer
        $customerObj = \DB::table('ser_customers')->where('company_id', Auth::user()->company_id)->where('type',$type)->select('customer_id')->latest('id')->first();
    if ($customerObj) {
        $orderNr = $customerObj->customer_id;
        $removed1char = substr($orderNr, 1);
        $customer_id = $stpad = '#' . str_pad($removed1char + 1, 8, "0", STR_PAD_LEFT);
    } else {
        $customer_id = '#' . str_pad(1, 8, "0", STR_PAD_LEFT);
    }
    
        $message =  DB::table('ser_customers')->insert(
                array(
            'client_name' => $request->name,
            'customer_id' => $customer_id,
            'type' => $type,
            'phone' => $request->mobile,
            'address' => $request->address,
            'company_id' => Auth::user()->company_id,
            'email' => $request->email,
            'dates' => date("Y-m-d"),
            'created_at' => date('Y-m-d H:i:s'),
                ));

       // dd($customer_id);
        // Invoice Id

             $companyname=\DB::table('companies')->where('id', Auth::user()->company_id)->select('company_name')->first();
            //dd($companyname);
            $invoice_company_name=str_replace(" ","",$companyname->company_name);
            $companyname=substr($invoice_company_name, 0, 3);
            $check_max_invoice_no=\DB::table('serv_invoice_nos')->where('company_id', Auth::user()->company_id)->where('type',$type)->orderBy('id', 'desc')->first();
            //dd($check_max_invoice_no);
            if($check_max_invoice_no)
            {
                $companyid=(Auth::user()->company_id);
                $replacedata=$companyname.'STU'.$companyid;

                $invoiceid=str_replace($replacedata,'',$check_max_invoice_no->id)+1;
               // dd($replacedata,$check_max_invoice_no->invoice_no);
                $invoicelen=4-strlen($invoiceid);
                //dd($invoiceid);
                $finalid='';
                if($invoicelen != 0){
                    for($i=0;$i<$invoicelen;$i++)
                    {
                        if($i==0)
                        {
                             $finalid='0'.$invoiceid;   
                        }else
                        {
                            $finalid='0'.$finalid;
                        }
                    }

                }else{
                    $finalid=$invoiceid;
                }
                $request['invoice_id']=$companyname.'-STU-'.Auth::user()->company_id.$finalid;
            }
            else
            {
                $request['invoice_id']=$companyname.'-STU-'.Auth::user()->company_id.'0001';
                $invoice=$request['invoice_id'];
            }
             $invoice_ids=$request['invoice_id'];

            
              DB::table('serv_invoice_nos')->insert([
                //'service_id' => $customer_id,
                'customer_id' => $customer_id,
                'invoice_no' => $invoice_ids,
                'subtotal' => $request->subtot,
                'payment_mode' => $request->pmMode,
                'type' => $type,
                'cheq_no' => $request->cheqno,
                'cheq_dt' => $request->cheqdate,
                'bank_name' => $request->bank_name,
                'transaction_no' => $request->trans_no,
                'online_bank_name' => $request->bank_name1,
                'company_id' => Auth::user()->company_id,
                 'paid_amt' => $request->amountRecieved,
                'due_amt' => $request->due_amount,
                 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                
                ]);
            
             //dd($request->due_amount,$input,$request->input('total_photo')) ;
         for ($i = 0; $i < $request->input('total_photo'); $i++) {
        $result=DB::table('service_bills')->insert(
            array(
            'customer_id' =>$customer_id,
            'invoice_no' =>$invoice_ids,
            'size' => $request['sizes'][$i],
            'quantity' => $request['qty'][$i],
            'type' => $type,
            //'price' => $request['rate'][$i],
            'amount' => $request['amt'][$i],
           // 'particulars' => $request['service_name'][$i],
            'delivery_date' => $request['deliverd_date'][$i],
            //'units' => $request['units'][$i],
           'company_id' => Auth::user()->company_id,
            'bill_createdBy' => Auth::user()->username,
            'bill_date' => date('Y-m-d'),
             'created_at' => date('Y-m-d H:i:s'),
            
                    ));
        }
        
       
        $company=\DB::table('companies')->where('id', Auth::user()->company_id)->first();
        //dd($company);
        $report = DB::table('service_bills')->select('service_bills.*')
            ->where('service_bills.invoice_no',$invoice_ids)
            ->where('service_bills.type',$type)
            ->where('service_bills.company_id',Auth::user()->company_id)->get();
         //dd($report); 
        $invoice_details = DB::table('serv_invoice_nos')->where('invoice_no',$invoice_ids)
        ->where('type',$type)->where('company_id',Auth::user()->company_id)->first();
           // dd($invoice_details,$report);
       
        return view('backend.pdfbill.studiobill', compact('report','invoice_details','company'));
    }
}
