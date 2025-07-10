const objXHR = new XMLHttpRequest();
let elemInput = null;
let elemOutput = null;
let strInput = "";
let strOutput = "";

document.addEventListener("DOMContentLoaded", _ => {
  elemInput = document.querySelector("#input");
  elemOutput = document.querySelector("#output");
  elemInput.addEventListener("keyup", fnCallPhp);
});

function fnCallPhp() {
  strInput = elemInput.value;
  const strUri = "includes/plzort.php?q=" + encodeURIComponent(strInput);
  objXHR.open("get", strUri, true);
  objXHR.addEventListener("load", fnUpdatePage);
  objXHR.send(null);
}

function fnUpdatePage() {
  let objJSON = null;
  
  if( objJSON = JSON.parse(objXHR.responseText)) {
    strOutput = "<table>";

    for( let intRow in objJSON ) {
      intRow = parseInt(intRow);

      if( objJSON[intRow].error ) {
        strOutput = objJSON[intRow].error;
        break;
      }

      strOutput += "<tr>";

      strOutput += "<td>" + objJSON[intRow].ort + "</td>";
      strOutput += "<td>" + objJSON[intRow].plz + "</td>";
      strOutput += "<td>" + objJSON[intRow].bundesland + "</td>";

      strOutput += "</tr>";
      
    }

    strOutput += "</table>";
  }

  elemOutput.innerHTML = strOutput;
}