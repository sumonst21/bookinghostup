var _validFileExtensions = [".jpg", ".jpeg", ".bmp", ".gif", ".png", ".pdf"];
function ValidateSingleInput(oInput) {

   var fileSize = document.getElementById('mphb_documentId').files[0].size;

    if (oInput.type == "file") {
        var sFileName = oInput.value;
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }

            }
            if (!blnValid) {
                alert("Sorry, " + sFileName + " is invalid, allowed extensions are: " + _validFileExtensions.join(", "));
                oInput.value = "";
                return false;
            }
        }
    }


    if ( fileSize > 2097152 ) {
      alert("Sorry, " + sFileName + " is invalid, Maximum File Size is 2MB" );
      return false;
    }

    return true;
}
