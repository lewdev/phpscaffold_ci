<?php
function ci_css() {
    // css/style.css
    return "
/*------------------------------------*\
	MAIN
\*------------------------------------*/
html{
    font-family:\"Helvetica Neue\", Arial, sans-serif;
    color:#e4eef6;
    background:-moz-linear-gradient(-90deg,#5998c7,#4a8ec2) fixed;
    background:-webkit-gradient(linear,left top,left bottom,from(#5998c7),to(#4a8ec2)) fixed;
    background:black; /*#4a8ec2;*/
}
body{
    background:none;
    padding-top:50px;
    text-shadow:0 -1px 0 rgba(0,0,0,0.5), 0 1px 0 rgba(255,255,255,0.25);
    background-color:#000055;
    width:960px;
    margin-left: auto;
    margin-right: auto;
    padding:10px;
}
#page{
    margin:0 20px;
    float:none;
}
#nav { background-color:black; margin-left:auto; margin-right:auto; }
#nav li a {    padding:0 10px; }
#nav li li {
    padding:5px 10px;
    background-color:black;
    white-space:nowrap;
}
#nav li a:hover {
    text-decoration:none;
    background-color:gray;
}
article { }
article a { color:gray; 
    text-shadow:none; }
article a:hover { }
.browse td {
    padding:5px; margin:0px;
    font-size: 11pt;
}
.actionsnav {
	background-color:black;
	border:1px dotted white;
	float:right;
	font-size:10pt;
	padding:5px;
	margin:5px;
}
.actionsnav ul {
    margin-left:20px;
    margin-right:auto;
    margin-bottom:auto;
}
/*------------------------------------*\
	CENTRED NAV
\*------------------------------------*/
/*
http://csswizardry.com/2011/01/create-a-centred-horizontal-navigation/
Add a class of centred/centered to create a centred nav.
*/
#nav.centred,#nav.centered {
    text-align:center; }
#nav.centred li,#nav.centered li {
    display:inline;
    float:none;
}
#nav.centred a,#nav.centered a{
    display:inline-block;
}
/*------------------------------------*\
	TYPE
\*------------------------------------*/
h1{
    font-weight:bold;
    line-height:1;
}
a{ color:inherit; }

/*------------------------------------*\
	IMAGES
\*------------------------------------*/
#logo{ margin-bottom:1.5em; }

/*------------------------------------*\
	NARROW
\*------------------------------------*/
/* CSS for tablets and narrower devices */
@media (min-width: 721px) and (max-width: 960px){
}
/*--- END NARROW ---*/

/*------------------------------------*\
	MOBILE
\*------------------------------------*/
/* CSS for mobile devices. Linearise it! */
@media (max-width: 720px) {
    body{ font-size:0.75em; }
}
/*--- END MOBILE ---*/
";
    }