<?php
header('Access-Control-Allow-Origin: *');

header('Access-Control-Allow-Methods: GET, POST');

header("Access-Control-Allow-Headers: X-Requested-With");
?>
<html>
<head>
<meta charset="UTF-8">
<title>项目管理系统</title>
<link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
<link rel="icon" type="image/png" href="images/favicon-16x16.png" sizes="16x16" />
<link href='vendor/swaggervel/css/typography.css' media='screen' rel='stylesheet' type='text/css'/>
<link href='vendor/swaggervel/css/reset.css' media='screen' rel='stylesheet' type='text/css'/>
<link href='vendor/swaggervel/css/screen.css' media='screen' rel='stylesheet' type='text/css'/>
<link href='vendor/swaggervel/css/reset.css' media='print' rel='stylesheet' type='text/css'/>
<link href='vendor/swaggervel/css/print.css' media='print' rel='stylesheet' type='text/css'/>

<script src="vendor/swaggervel/lib/shred.bundle.js"></script>
<script src="vendor/swaggervel/lib/underscore-min.js"></script>
<script src="vendor/swaggervel/lib/swagger.js"></script>


<script src='vendor/swaggervel/lib/jquery-1.8.0.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/jquery.slideto.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/jquery.wiggle.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/jquery.ba-bbq.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/handlebars-2.0.0.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/js-yaml.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/lodash.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/backbone-min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/swagger-ui.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/highlight.7.3.pack.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/jsoneditor.min.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/marked.js' type='text/javascript'></script>
<script src='vendor/swaggervel/lib/swagger-oauth.js' type='text/javascript'></script>

<!-- enabling this will enable oauth2 implicit scope support -->
{{--    {{ HTML::script('packages/jlapp/swaggervel/lib/swagger-oauth.js' , array(), $secure); !!}--}}

        <script type="text/javascript">
                $(function () {
                                window.swaggerUi = new SwaggerUi({
url: "{!! $urlToDocs !!}",
dom_id: "swagger-ui-container",
supportedSubmitMethods: ['get', 'post', 'put', 'delete'],
onComplete: function (swaggerApi, swaggerUi) {
log("Loaded SwaggerUI");
@if(isset($requestHeaders))
@foreach($requestHeaders as $requestKey => $requestValue)
window.authorizations.add("{!!$requestKey!!}", new ApiKeyAuthorization("{!!$requestKey!!}", "{!!$requestValue!!}", "header"));
@endforeach
@endif
/*if (typeof initOAuth == "function") {
  initOAuth({
  clientId: "your-client-id",
  realm: "your-realms",
  appName: "your-app-name"
  });
  }*/
$('pre code').each(function (i, e) {
        hljs.highlightBlock(e)
        });
},
onFailure: function (data) {
                   log("Unable to Load SwaggerUI");
           },
docExpansion: "none"
});

$('#input_apiKey').change(function () {
                var key = $('#input_apiKey')[0].value;
                log("key: " + key);
                if (key && key.trim() != "") {
                log("added key " + key);
                window.authorizations.add("key", new ApiKeyAuthorization("{!! Config::get('swaggervel.api-key') !!}", key, "query"));
                } else {
                window.authorizations.remove("key");
                }
                })
window.swaggerUi.load();
});
</script>
</head>
<body class="swagger-section">
<div id='header'>
<div class="swagger-ui-wrap">
<a id="logo1" style="text-decoration:none;font-size:24px;font-weight:blod;color:#fff;" href="https://github.com/turtleliangzi/laravel-dingo">PMS</a>
<!--
<form id='api_selector'>
<div class='input icon-btn'>
<image id = "show-pet-store-icon" title = "Show Swagger Petstore Example Apis" src="vendor/swaggervel/images/pet_store_api.png" />
</div>
<div class='input icon-btn'>
<image id = "show-wordnik-dev-icon" title = "Show Wordnik Developer Apis" src="vendor/swaggervel/images/wordnik_api.png" />
</div>
<div class='input'><input placeholder="http://example.com/api" id="input_baseUrl" name="baseUrl"
type="text"/></div>
<div class='input'><input placeholder="api_key" id="input_apiKey" name="apiKey" type="text"/></div>
<div class='input'><a id="explore" href="#">Explore</a></div>
</form>
-->
</div>
</div>

<div id="message-bar" class="swagger-ui-wrap">&nbsp;</div>
<div id="swagger-ui-container" class="swagger-ui-wrap"></div>
</body>
</html>

