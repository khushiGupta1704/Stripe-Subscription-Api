<?php
$stripe_key=$response['stripe_key'];
$session_id=$response['session_id'];
?>
<section class="welcome">
		<div class="container">
			<div class="row justify-content-center">
				<div class="col-lg-6 col-md-8">
					<div class="welcome-inner" data-aos="zoom-in">
						<h1 data-aos="fade-down"><span>Thankyou for purchase.</span></h1>
						<a data-aos="fade-up" href="#" class="btn btn-skin btn-block">Loading....</a>
					</div>
				</div>
			</div>
		</div>
    </section>

<script src="https://js.stripe.com/v3/"></script>
<script src="{{asset('https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js')}}"></script>
<script type="text/javascript">

var stripe = Stripe("{{$stripe_key}}");
$(document).ready(function() {
	alert(5);
  stripe.redirectToCheckout({
            sessionId: "{{$session_id}}",
          })
          .then(handleResult);	
	
});

var handleResult = function (result) {
  if (result.error) {
    console.log(result.error);
  }
};
</script>