<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StripeSubscriptionController extends Controller
{
    	public function subscriptionPage(){
		return view('subscription');
	}
		public function subscriptionGatewayDirection(){
			$s_key="your_secret_key";
			$p_key="your_publish_key";
			
			//creating object of stripe
			$stripe = new \Stripe\StripeClient($s_key);
			
			//create that user as your customer on stripe
			$customer=$this->createCustomer($stripe);
			
			//register your product on stripe
			$product_id=$this->createProduct($stripe);

			////register your price on stripe
			if(!empty($product_id)){
				$price_id=$this->createPrice($stripe,$product_id);
			}
			
			//ccreating session for user to create subscription
			if(!empty($product_id) && !empty($price_id) && !empty($customer) && isset($customer->id)){
				
				$subscription=$stripe->checkout->sessions->create([
				  'success_url' => route('save.subscription')."?session_id={CHECKOUT_SESSION_ID}",
				  'cancel_url' => route('home.page'),
				  'line_items' => [
					[
					'price_data'=>
						[
						'currency'=>'usd',
						'product'=>$product_id,
						'unit_amount'=>100,
						'recurring'=>[
							'interval'=>'day',
							'interval_count'=>1,
							],
							
							
						],
					  'quantity' => 1,
					],
				  ],
				  'customer'=>$customer->id,
				  'mode' => 'subscription',
				]);
				$response=[];
				if(!empty($subscription)){
					$response['session_id']=$subscription->id;
					$response['stripe_key']=$p_key;
					
					//returning to view for stripe payment 
					return view('stripe')->with(['response'=>$response]);
				}
			}
			
		}
		public function createCustomer($stripe){
			$customer=$stripe->customers->create([
				'email'=>'guptakhusi259@gmaail.com',
			  'description' => 'My First Test Customer',
			]);
			
			//store customer Id for your product in database for future use
			return $customer;
		}
		public function createProduct($stripe){
			$product_id="";
			$productResponse=$stripe->products->create([
			  'name' => 'Gold Special',
			]);
			if(!empty($productResponse) && isset($productResponse->id)){
				//store product Id for your product in database for future use
				$product_id=$productResponse->id;
			}
			return $product_id;
		}
		public function createPrice($stripe,$product_id){
			$price_id="";
			$priceResponse=$stripe->prices->create([
				  'unit_amount' => 2,
				  'currency' => 'usd',
				  'recurring' => ['interval' => 'month'],
				  'product' => $product_id,
				]);
				
				if(!empty($priceResponse) && isset($priceResponse->id)){
					//store price Id for your product in database for future use
				$price_id=$priceResponse->id;
			}
			return $price_id;
		}
		public function SaveSubscription(Request $request){
			$s_key="your_secret_key";
			
			//creating object for stripe
			$stripe = new \Stripe\StripeClient($s_key);
			
			//gettign sesssion details and subscription Id 
			$response=$stripe->checkout->sessions->retrieve($request->session_id);
			
			//retrieveing subscription details from stripe
			if(!empty($response) && isset($response->subscription)){
			$stripe_response=$stripe->subscriptions->retrieve(
			$response->subscription,
			[]
			);
			//save data in database for future use
			}
		}
	
}
