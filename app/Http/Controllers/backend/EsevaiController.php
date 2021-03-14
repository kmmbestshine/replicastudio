<?php

namespace App\Http\Controllers\backend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use DB;

class EsevaiController extends Controller
{
    public function create()
    {
       // dd('esevai create');
        //$this->checkpermission('module-create');
        $services = DB::table('eservices')->where('company_id', Auth::user()->company_id)->get();
        $units = DB::table('units')->where('company_id', Auth::user()->company_id)->get();
        
        return view('backend.e-sevai.create', compact('services','units'));
    }

    public function esevaibillverify(Request $request)
    {
      $input = \Request::all();
      $name = $request->name;
      $mobile = $request->mobile;
      $address = $request->address;
      $email = $request->email;
      $deliverd_date = $request->deliverd_date;
      $service_id = $request->service_id;
      $units = $request->units;
      $qty = $request->qty;
      $rate = $request->rate;
      $counts= sizeof($service_id);
     // dd($counts);
     // $amt = $request->amt;



      foreach ($service_id as $key => $value) {
       $services[] = DB::table('eservices')->where('company_id', Auth::user()->company_id)
       ->where('id',$value)->get();
      }
      foreach ($services as $key1 => $value1) {
        foreach ($value1 as $key => $value) {
         $service_name[]=$value->name;
       $service_ids[]=$value->id;
        }
      }
      $grand=0;
      for($i=0; $i < $counts; $i++){
        $amt[]= $qty[$i] * $rate[$i];
        $price =$qty[$i] * $rate[$i];
        $grand += $price;
      }
      
      
     // dd($service_name,$service_id);
       // dd($input,$address, $mobile,$service_id,$amt);
        return view('backend.e-sevai.verify', compact('grand','name','mobile','address','email','deliverd_date','service_id','units','qty','rate','amt','service_name'));
    }
    public function esevaibillstore(Request $request)
    {
        $input = \Request::all();
       // dd($input);
        $type='eservice';
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
                $replacedata=$companyname.'ESB'.$companyid;

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
                $request['invoice_id']=$companyname.'-ESB-'.Auth::user()->company_id.$finalid;
            }
            else
            {
                $request['invoice_id']=$companyname.'-ESB-'.Auth::user()->company_id.'0001';
                $invoice=$request['invoice_id'];
            }
             $invoice_ids=$request['invoice_id'];

           //  dd($request['invoice_id']);
         
         

        // dd($request->customer_id[0]);

         
       
           // dd($inv_ids,$request->subtot,$request->gsttotal,$request->grandtotal,$request->amountdiscount,$request->amountRecieved,$due_amount);

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
            
            // dd($request->input('total_service'),$input) ;
         for ($i = 0; $i < $request->input('total_service'); $i++) {
        $result=DB::table('service_bills')->insert(
            array(
            'customer_id' =>$customer_id,
            'invoice_no' =>$invoice_ids,
            'service_id' => $request['service_id'][$i],
            'quantity' => $request['qty'][$i],
            'price' => $request['rate'][$i],
            'type' => $type,
            'amount' => $request['amt'][$i],
            'particulars' => $request['service_name'][$i],
            'delivery_date' => $request['deliverd_date'][$i],
            'units' => $request['units'][$i],
           'company_id' => Auth::user()->company_id,
            'bill_createdBy' => Auth::user()->username,
            'bill_date' => date('Y-m-d'),
             'created_at' => date('Y-m-d H:i:s'),
            
                    ));
        }
        
       
        $company=\DB::table('companies')->where('id', Auth::user()->company_id)->first();
        
        $report = DB::table('service_bills')->join('eservices', 'eservices.id', '=', 'service_bills.service_id')
            ->select('service_bills.*', 'eservices.name')
            ->where('service_bills.invoice_no',$invoice_ids)
            ->where('service_bills.type',$type)
            ->where('service_bills.company_id',Auth::user()->company_id)->get();
         //dd($report); 
        $invoice_details = DB::table('serv_invoice_nos')->where('invoice_no',$invoice_ids)
        ->where('type',$type)->where('company_id',Auth::user()->company_id)->first();
           // dd($invoice_details,$report);
       
        return view('backend.pdfbill.servicebill_esevai', compact('report','invoice_details','company'));
    }
public function eservicelist()
    {
       // dd('esevai create');
        //$this->checkpermission('module-create');
        $services = DB::table('eservices')->where('company_id', Auth::user()->company_id)->get();
       
        return view('backend.e-sevai.eservicelist', compact('services'));
    }
    public function eservicecreate()
    {
       // dd('esevai create');
        //$this->checkpermission('module-create');
       
        return view('backend.e-sevai.eservicecreate');
    }
    public function eservicestore(Request $request)
    {
        
        //$this->checkpermission('module-create');
        $input=\Request::all();
       // dd($input);
        $this->validate($request, [
            'name' => 'required',
            'status' => 'required',
        ]);
        //dd('esevai store');
       $message = DB::table('eservices')->insert([
                'company_id' => Auth::user()->company_id,
                'name' => $request->name,
                'status' => $request->status,
                 'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
                
                ]);
       if ($message) {
            return redirect()->route('eservice.list')->with('success_message', 'successfully created ');
        } else {
            return redirect()->route('esevai.service.create')->with('error_message', 'Failed To create');
        }
    }
}
