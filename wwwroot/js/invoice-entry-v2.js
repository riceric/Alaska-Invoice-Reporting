/**  
 * Convert array into object literal
 * i.e. ['bobby':'', 'sue':'','smith':'']
 * usage: 25 in oc(['25', '10','15']) // returns true 
 */
function oc(a)
{
	var o = {};
	for(var i=0;i<a.length;i++)
	{
		o[a[i]]='';
	}
	return o;
}
/**  
 * Insert decimals for prescription values with more than 3 digits (+/-)
 */
function insertDecimal(n)
{
	var strAbs = n.replace("-", ""); //Strip out negative
	if ((strAbs.length < 3) || (strAbs.indexOf(".") != -1))
	{
		return n;
	}
	else {
		var str = n.substring(0,n.length-2);
		//alert("str: "+str);
		//alert("concatted: "+str.concat(".",n.substring(n.length-2,n.length)));
		var newNum = str.concat(".",n.substring(n.length-2,n.length));
		return newNum; //Concat a decimal and the last 2 digits
	}
}

$(function() {
    // mini jQuery plugin that formats to two decimal places
    (function($) {
        $.fn.rxFormat = function() {
            this.each( function( i ) {
                $(this).change( function( e ){
                    if( isNaN( parseFloat( this.value ) ) ) return;
					var newNum = parseFloat(insertDecimal(this.value));
                    this.value = newNum.toFixed(2);
                });
            });
            return this; //for chaining
        }
    })( jQuery );

	//Autocomplete Arrays
	var v2799Options = [
		"MISC FRAME",
		"OTHER LENSES",
		"VISION SERVICE, TINT"
	];
	var v2799TypeOptions = [
		"FRAME",
		"LENS",
		"MISC"
	];
	var dmoOptions = [
		"ATFM",
		"ATFP",
		"ATMM",
		"ATMP",
		"KFM",
		"KFP",
		"KMM",
		"KMP",
		"T"
	];
	// Make sure that getPrescriptionVCode matches these options
	var availableLenses = [
		"SV - Single Vision",
		"ST28 - Straight Top 28",
		"ST35 - Straight Top 35",
		"Round Seg",
		"7X28 Trifocal",
		"7X35 Trifocal"
	];
	
	jQuery.validator.addMethod("omulti", function(value, element) { 
	 return this.optional(element) || value in oc(availableLenses); 
	}, jQuery.format("Invalid lens type"));
	jQuery.validator.addMethod("gender", function(value, element) { 
	 return this.optional(element) || value in oc(dmoOptions); 
	}, jQuery.format("Invalid gender code"));
	
	//Masks
	$("#odsph,#odcyl,#ossph,#oscyl,#amount").rxFormat();
	$("#patientDOB,#dateOrder").mask("9999/99/99");
	//Form validation
    $("#invoiceForm").validate({
		focusCleanup: true,
		rules: {
			jobid: {
				minlength:6,
				digits: true
			},
			recipientID: {
				minlength:10,
				digits: true,			
				required: true
			},
			sex: { //
				required: true,
				gender:true,
				remote: {
					url: "validator.php",
					type: "post",
					data: {
						mode: function() {
							return $("#mode").val();
						},
						recipientID: function() {
							return $("#recipientID").val();
						},
						sex: function() {
							return $("#sex").val();
						}
					}
				}
			},
			priorAuthNum: {
				minlength:8,
				digits: true
			},
			patientDOB: {
				required: true,
				remote: "validator.php"
			},
			dateOrder: {
				required: true,
				remote: "validator.php"
			},
			amount: {
				number: true
			},
			odsph:{
				required: true,
				number: true,
				range:[-48,48]
			},
			odmulti: {
				required: true,
				omulti: true
			},
			ossph: {
				required: true,
				number: true,
				range:[-48,48]
			},
			odcyl:{
				number: true,
				range:[-25,25]
			},
			oscyl: {
				number: true,
				range:[-25,25]
			},
			osmulti: {
				required: true,
				omulti: true
			},
			miscServiceCost: {
				required: "#miscService:checked",
				min: 0
			},
			miscServiceDesc: {
				required: "#miscService:checked"
			},
			miscServiceType: {
				required: "#miscService:checked"
			},
			frameName: {
				required: "#frameSupplied:checked"
			}
		},
		messages: {
			jobid: {
				minlength: "Must be 6 digits long"
			},
			recipientID: {
				minlength: "Must be 10 digits long",
				remote: "This patient is not eligible for another frame this year."
			},
			sex: {
				remote: "This patient is not eligible for another frame this year. <a href=\"invoice-entry.php\">Reset</a>"
			},
			priorAuthNum: {
				minlength: "Must be 8 digits long"
			},
			patientDOB: {
				remote: "Please enter a valid date."
			},
			dateOrder: {
				remote: "Please enter a valid date."
			},
			odsph:{
				range:"Cannot exceed +/-48"
			},
			ossph: {
				range:"Cannot exceed +/-48"
			},
			odcyl:{
				range:"Cannot exceed +/-25"
			},
			oscyl: {
				range:"Cannot exceed +/-25"
			},
			miscServiceCost: {
				required: "Specify a cost."
			},
			miscServiceDesc: {
				required: "Add a description."
			},
			miscServiceType: {
				required: "Add a service type."
			},
			frameName: {
				required: "Specify a frame"
			}
		}
	});
	
	//Remove restriction on eligibility of prior authorization code is provided
	$('#priorAuthNum').change(function() {
		if ($(this).val() != "") {
			$("#sex").rules("remove");
		}
		else {
			$("#sex").rules("add");
		}
	});
	//Echo odMulti in osMulti
	$('#odmulti').focusout(function() {
		$('#osmulti').val($('#odmulti').val());
	});
	
	//Trigger for v2799 description autocomplete
	$( "#miscServiceDesc" ).autocomplete({
        source: v2799Options,
        change: function (event, ui) {
            //if the value of the textbox does not match a suggestion, clear its value
            if ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0) {
                $(this).val('');
            }
        }
    }).live('keydown', function (e) {
        var keyCode = e.keyCode || e.which;
        //if TAB or RETURN is pressed and the text in the textbox does not match a suggestion, set the value of the textbox to the text of the first suggestion
        if((keyCode == 9 || keyCode == 13) && ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0)) {
            $(this).val($(".ui-autocomplete li:visible:first").text());
        }
    });		
	$( "#miscServiceType" ).autocomplete({
        source: v2799TypeOptions,
        change: function (event, ui) {
            //if the value of the textbox does not match a suggestion, clear its value
            if ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0) {
                $(this).val('');
            }
        }
    }).live('keydown', function (e) {
        var keyCode = e.keyCode || e.which;
        //if TAB or RETURN is pressed and the text in the textbox does not match a suggestion, set the value of the textbox to the text of the first suggestion
        if((keyCode == 9 || keyCode == 13) && ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0)) {
            $(this).val($(".ui-autocomplete li:visible:first").text());
        }
    });		
	//Trigger for gender/frame autocomplete
	$( "#sex" ).autocomplete({
        source: dmoOptions,
        change: function (event, ui) {
            //if the value of the textbox does not match a suggestion, clear its value
            if ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0) {
                $(this).val('');
            }
        }
    }).live('keydown', function (e) {
        var keyCode = e.keyCode || e.which;
        //if TAB or RETURN is pressed and the text in the textbox does not match a suggestion, set the value of the textbox to the text of the first suggestion
        if((keyCode == 9 || keyCode == 13) && ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0)) {
            $(this).val($(".ui-autocomplete li:visible:first").text());
        }
    });	
	//Trigger for lens type autocomplete
	$( "#odmulti, #osmulti" ).autocomplete({
        source: availableLenses,
        change: function (event, ui) {
            //if the value of the textbox does not match a suggestion, clear its value
            if ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0) {
                $(this).val('');
            }
        }
    }).live('keydown', function (e) {
        var keyCode = e.keyCode || e.which;
        //if TAB or RETURN is pressed and the text in the textbox does not match a suggestion, set the value of the textbox to the text of the first suggestion
        if((keyCode == 9 || keyCode == 13) && ($(".ui-autocomplete li:textEquals('" + $(this).val() + "')").size() == 0)) {
            $(this).val($(".ui-autocomplete li:visible:first").text());
        }
    });

	//Hide modal dialogs
	$( "#dialog-delete" ).hide();
	
	//List of frame choices
	populateFrames();
	$('#frameSupplied').change(function() {
		populateFrames();
	});
	
	//Check for BAL
	$('#odbal,#osbal').change(function() {
	addBal(this);
});

});

function populateFrames() {
	var select = $('#frameName');
	var options = select.attr('options');
	var frameid = '<?php echo $frameName; ?>';
	$('option', select).remove();
	
	if ($("#frameSupplied:checked").length > 0)
	{
		$.getJSON('ac-getframes.php', function(data) {
			$.each(data, function(index, array) {
				if (array['id'] == frameid) {
					options[options.length] = new Option(array['name'],array['id'],true,true); 
				}
				else {
					options[options.length] = new Option(array['name'],array['id']); }
			});
		});
	}
	else 
	{
		options[0] = new Option("--No frame--","");
	}
}

function addBal(o)
{
	if ($("#odbal:checked").length > 0 || $("#osbal:checked").length > 0) 
	{
		$("#odbal,#osbal").removeAttr("checked");
		if (o.id == "odbal") {
			$("#odbal").attr("checked", "checked");
			setBal(o); //Sync rx fields
			$("#ossph, #oscyl, #ospsm, #osmulti").keyup(function() {
				setBal(o);
			});
		}
		else if (o.id == "osbal") {
			$("#osbal").attr("checked", "checked");
			setBal(o); //Sync rx fields
			$("#odsph, #odcyl, #odpsm, #odmulti").keyup(function() {
				setBal(o);
			});
		}		
	}
}

function setBal(o)
{
	if (o.id == "odbal") {
		$("#odsph").val($("#ossph").val());
		$("#odcyl").val($("#oscyl").val());
		$("#odpsm").val($("#ospsm").val());
		$("#odmulti").val($("#osmulti").val());
	}
	else if (o.id == "osbal") {
		$("#ossph").val($("#odsph").val());
		$("#oscyl").val($("#odcyl").val());
		$("#ospsm").val($("#odpsm").val());
		$("#osmulti").val($("#odmulti").val());
	}
	
}

//Dialog: Confirm delete
function showConfirm() {
	$( "#dialog-delete" ).dialog({
		resizable: false,
		height:140,
		modal: true,
		buttons: {
			"Delete this order": function() {
				alert("Show delete dialog");
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		}
	});
}

