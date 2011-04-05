function ReplaceContentInContainer(id, content) {
	var container = document.getElementById(id);
	container.innerHTML = content;
}

function sha1Fields(aFieldArray) {
	// replace the plaintext of the fields with the SHA1 digest of those fields before submitting over the wire!
	for (i = 0; i < aFieldArray.length; ++i) {
		if (aFieldArray[i].value != "") {
			aFieldArray[i].value = hex_sha1(aFieldArray[i].value);
		}
	}
	return true;
}

function doRealLogin(inRealForm, inUserForm) {
	inRealForm.username.value = inUserForm.username.value;
	inRealForm.password.value = hex_sha1(inUserForm.password.value);
	inRealForm.submit();
	return true;
}

function gotoPage(inForm, inPage) {
	inForm.page.value = inPage;
	inForm.submit();
}

function submitOnEnter(myfield, evt)
{
	var keycode;
	if (window.event)
		keycode = window.event.keyCode;
	else if (evt)
		keycode = evt.which;
	else
		return true;
	
	if (keycode == 13)
	{
		myfield.form.submit();
		return false;
	}
	else
		return true;
}

function isMozilla()
{
	var isKHTML = sUserAgent.indexOf("KHTML") > -1
    	|| sUserAgent.indexOf("Konqueror") > -1
    	|| sUserAgent.indexOf("AppleWebKit") > -1;
    var isMoz = sUserAgent.indexOf("Gecko") > -1
    	&& !isKHTML;
    
    return isMoz;
}
