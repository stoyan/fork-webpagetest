<?php
    $category = "Quick";
    $blockingCSSReqs = array();
    foreach ($requests as $request) {
        if( $request['renderBlocking'] === "blocking"  && strpos($request['url'], "font") === false && $request['contentType'] === "text/css" ){
            array_push($blockingCSSReqs, $request['url'] );
        }
    }

    if( count($blockingCSSReqs) > 0 ){
        $assessment[$category]["opportunities"][] = array(
            "title" =>  count($blockingCSSReqs) . " externally-referenced CSS file". (count($blockingCSSReqs) > 1 ? "s are" : " is") ." blocking page rendering.",
            "desc" =>  "By default, references to external CSS files will block the page from rendering while they are fetched and executed. Sometimes these files should block rendering, but can be inlined to avoid additional round-trips while the page is waiting to render.",
            "examples" =>  $blockingCSSReqs,
            "experiments" =>  array(
                (object) [
                    'title' => 'Inline external stylesheets.',
                    "desc" => '<p>This experiment will embed the contents of external stylesheets directly into the HTML within a <code>style</code> element. This increases the size of the HTML, but can often allow page page to display sooner by avoiding server round trips.</p>',
                    "expvar" => 'inline',
                    "expval" => $blockingCSSReqs
                ]
            ),
            "good" =>  false
        );
    } else {
        $assessment[$category]["opportunities"][] = array( 
            "title" =>  'You had no render-blocking external CSS files.',
            "desc" =>  "TBD importance notes here",
            "examples" =>  array(),
            "experiments" =>  array(),
            "good" =>  true
        );
}

?>