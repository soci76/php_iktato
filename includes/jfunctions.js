<script language="javascript" type="text/javascript">
  function calculate() {
    partner_id = document.catalog_form.partner_id.value;
    C1 = "24";
    document.catalog_form.c1.value = C1;
  };
        function Initcb (i) {
            if (i==1) { 
            var checkbox1 = document.getElementById ("cbraktarnev");
            var checkbox2 = document.getElementById ("cbfocsoportnev");
            var checkbox3 = document.getElementById ("cbcikkszamnev"); 
            if (checkbox1.addEventListener) { checkbox1.addEventListener ("CheckboxStateChange", OnChangeCheckbox, false); }
            if (checkbox2.addEventListener) { checkbox2.addEventListener ("CheckboxStateChange", OnChangeCheckbox, false); }
            if (checkbox3.addEventListener) { checkbox3.addEventListener ("CheckboxStateChange", OnChangeCheckbox, false); }}                  
            
            if (i==2) {
            var checkbox3 = document.getElementById ("cbdolgozonev");
            var checkbox4 = document.getElementById ("cbgtermeknev");
            if (checkbox3.addEventListener) { checkbox3.addEventListener ("CheckboxStateChange", OnChangeCheckboxg, false); }
            if (checkbox4.addEventListener) { checkbox4.addEventListener ("CheckboxStateChange", OnChangeCheckboxg, false); }}
            
            if (i==3) {
            var checkbox5 = document.getElementById ("cbptermeknev");
            if (checkbox5.addEventListener) { checkbox5.addEventListener ("CheckboxStateChange", OnChangeCheckboxp, false); }}
            
        }
        
        function OnChangeCheckbox (event) {
            //termek
            var cb1 = document.getElementById ("cbraktarnev");
            var cb2 = document.getElementById ("cbfocsoportnev"); 
            var cb3 = document.getElementById ("cbcikkszamnev");             
            if (cb1.checked) {
                if (cb2.checked) { 
                    if (cb3.checked) { location.href=location.pathname+ "?lm=111"; } 
                    else { location.href=location.pathname+ "?lm=110"; }}
                else {
                    if (cb3.checked) { location.href=location.pathname+ "?lm=101"; } 
                    else { location.href=location.pathname+ "?lm=100"; }}}                  
            else {
                if (cb2.checked) { 
                    if (cb3.checked) { location.href=location.pathname+ "?lm=011"; } 
                    else { location.href=location.pathname+ "?lm=010"; }}
                else {
                    if (cb3.checked) { location.href=location.pathname+ "?lm=001"; } 
                    else { location.href=location.pathname+ "?lm=000"; }}}   
        } 
        function OnChangeCheckboxg (event) {
            //termek
            var cb1 = document.getElementById ("cbdolgozonev");
            var cb2 = document.getElementById ("cbgtermeknev"); 
            if (cb1.checked) {
                if (cb2.checked) { location.href=location.pathname+ "?lm=11"; }
                else { location.href=location.pathname+ "?lm=10"; }}
            else {
                if (cb2.checked) { location.href=location.pathname+ "?lm=01"; }
                else { location.href=location.pathname+ "?lm=00"; }}
        } 
        function OnChangeCheckboxp (event) {
            //termek
            var cb1 = document.getElementById ("cbptermeknev");
            if (cb1.checked) { location.href=location.pathname+ "?lm=1"; }
            else { location.href=location.pathname+ "?lm=0"; }
        }         
        
</script>
