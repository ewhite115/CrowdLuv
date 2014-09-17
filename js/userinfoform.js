


/**
 *  handlers fo the Follower Settings form
 * 
 */

function animate_settingsupdated_notification(){
   $("#p_settingsupdated").fadeIn(1800, function(){
        $(this).fadeOut(1800);
    });
}





/**
 * Phone Formatting / Validation functions
 *
 */

var defaultPhone = "";


/* ************************************** */
function load() {
  /*
  
  */
  
  $('#mobile').val(defaultPhone);
  $('#mobile').focus();
  
  processPhone();
}


// -------------------------------------------------------------------------   
function clickPhone() {
  /*
  
  Called when the phone box is clicked 

  if ($('#mobile').val() == defaultPhone) {
    $('#mobile').val('');
  }
  */
  processPhone();
}


// -------------------------------------------------------------------------   
function processPhone() {
  /*
  
  Process the phone number
  
  */

  phone = $('#mobile').val();
    
  if (phone.length == 0) {
    $('#popup').fadeOut('fast');
  } else {
    $('#popup').fadeIn('fast');
  }
  
  //var country = $('#select_country').val();
  var country = 'US';
  
  var e164 = formatE164(country, phone);
  /*if (e164.substring(0, 1) == "+") {
    $('#phone_e164').html(e164);
  } else {
    $('#phone_e164').html('+');
  }*/
  //$('#phone_international').html(formatInternational(country, phone));
  //$('#phone_national').html(formatLocal(country, phone));
  var countryCode = countryForE164Number(formatE164(country, phone));
  if (countryCode.length == 0) { 
    //$('#phone_country').html('-');
  } else {
    //$('#phone_country').html(countryCode + " - " + countryCodeToName(countryCode));
  }
  //$('#phone_mobile_dial').html(formatNumberForMobileDialing(country, phone));
  if (isValidNumber(phone, country)) {
    $('#phone_valid').html('Yes');
  } else {
    $('#phone_valid').html('No');
  }
}


function validatePhone() {

    var country = 'US'; 
    phone = $('#mobile').val();
    var e164 = formatE164(country, phone);
    var countryCode = countryForE164Number(formatE164(country, phone));


    if (isValidNumber(phone, countryCode)) {
        $('#phone_valid').html('Thank you - ' + countryCode);
        return true;
    } else {
        $('#phone_valid').html("Please provide a valid mobile number to receive SMS alerts when your favorite performers come to your town. " + countryCode );
        return false;
    }

}





$(document).ready(function(){

    $("#p_settingsupdated").hide();
    //Change handler for the "Allow email contact from CrowdLuv" radio options
    $("[name=allow_cl_email]").change(function(){
        update_follower_setting("allow_cl_email", $(this).val());
        animate_settingsupdated_notification();
    });

    //Change handler for the "Allow SMS contact from CrowdLuv" radio options
    $("[name=allow_cl_sms]").change(function(){
        update_follower_setting("allow_cl_sms", $(this).val());
        animate_settingsupdated_notification();
    });

    //Change handler for the email address text box 
    $("[name=email]").change(function(){
        update_follower_setting("email", $(this).val());
        animate_settingsupdated_notification();
    });

    //Change handler for the mobile number box
    $("[name=mobile]").change(function(){
        if( ! validatePhone()) return;
        update_follower_setting("mobile", $(this).val());
        animate_settingsupdated_notification();
    });

    //Change handler for the "Allow SMS contact from CrowdLuv" radio options
    $("[name=firstname]").change(function(){
        update_follower_setting("firstname", $(this).val());
        animate_settingsupdated_notification();
    });

    //Change handler for the "Allow SMS contact from CrowdLuv" radio options
    $("[name=lastname]").change(function(){
        update_follower_setting("lastname", $(this).val());
        animate_settingsupdated_notification();
    });

});






