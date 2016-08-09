<?php
require_once "config.php";
ini_set('include_path', $includePath);

require_once "HtmlBuilder.php";
use \Nodes\HtmlNode as HtmlNode;

error_reporting(E_ALL);
ini_set('display_errors', 1);

class Abbreviation {
    public $abbreviation;
    public $long_description;
    
    public function __construct($abbrev, $desc) {
        $this->abbreviation = $abbrev;
        $this->long_description = $desc;
    }
}

$abbreviations = array(
    new Abbreviation("SCTP", "Stream Control Transmission Protocol"),
    new Abbreviation("PR-SCTP", "Partial Reliability Extension for SCTP"),
    new Abbreviation("SUT", "System under Test"),
    new Abbreviation("IUT", "Implementation under Test"),
    new Abbreviation("V", "Valid Behaviour"),
    new Abbreviation("I", "Invalid Behaviour"),
    new Abbreviation("O", "Inopportune behaviour"),
    new Abbreviation("TSN", "Transmission Sequence Number"),
    new Abbreviation("cwnd", "Congestion Window Size"),
    new Abbreviation("yasa", "yet another silly abbreviation"),
);

uasort($abbreviations, function ($a, $b) {
    return strcmp($a->abbreviation, $b->abbreviation);
});

class ExternalReference {
    public $name, $id, $link;
    
    function __construct($name, $id, $link) {
        $this->name = $name;
        $this->id = $id;
        $this->link = $link;
    }
}

$external_references = array(
    new ExternalReference("RFC 4960 (SCTP)", "rfc4960", "https://tools.ietf.org/html/rfc4960"),
    new ExternalReference("RFC 3758 (PR-SCTP)", "rfc3758", "https://www.ietf.org/rfc/rfc3758.txt"),
    new ExternalReference("RFC 6458 (Sockets API Extensions for SCTP)", "rfc6458", "https://www.ietf.org/rfc/rfc6458.txt"),
    new ExternalReference("RFC 7496 (Additional Policies for SCTP)", "rfc7496", "https://www.ietf.org/rfc/rfc7496.txt"),
    new ExternalReference("Internet Draft - Stream Schedulers and User Message Interleaving for SCTP", "ndata05", "https://tools.ietf.org/html/draft-ietf-tsvwg-sctp-ndata-05"),
    new ExternalReference("Internet Draft - Load Sharing for SCTP", "loadsharing", "https://tools.ietf.org/html/draft-tuexen-tsvwg-sctp-multipath-05"),
);


class Testsuite {
    public $id, $folderName, $longName, $notice;
	public $test_cases = array();
    
    function __construct($id, $folderName, $longName, $notice = "") {
        $this->id = $id;
        $this->folderName = $folderName;
        $this->longName = $longName;
        $this->notice = $notice;
    }

	public function __toString() {
		$html = "";
        $html .= HtmlNode::get_builder("h2")->text($this->longName)->build();

        if (!empty($this->notice)) {
            $html .= HtmlNode::get_builder("p")->text("<strong>Notice:</strong> " . $this->notice)->build();
        }

        foreach ($this->test_cases as $test_case) {
            $html .= $test_case;
        }
	
		return $html;
	}
}

class Testcase {
    public $id = "", $precondition = "", $purpose = "", $references = "";
    
    private function build_tr_row ($name, $value) {
        $tr = HtmlNode::get_builder("tr")->build();
        $tr->addChildNode(HtmlNode::get_builder("td")->text($name)->build());
        $tr->addChildNode(HtmlNode::get_builder("td")->text($value)->build());
        return $tr;
    }
    
    public function __toString() {
        $table = HtmlNode::get_builder("table")->attribute("class", "test_case_table")->build();
        $tbody = HtmlNode::get_builder("tbody")->build();
        $table->addChildNode($tbody);
        
        $tr_id = $this->build_tr_row("ID", $this->id);
        $tr_precondition = $this->build_tr_row("Precondition", $this->precondition);
        $tr_purpose = $this->build_tr_row("Purpose", $this->purpose);
        $tr_references = $this->build_tr_row("References", $this->references);

        $tbody->addChildNode($tr_id);
        $tbody->addChildNode($tr_precondition);
        $tbody->addChildNode($tr_purpose);
        $tbody->addChildNode($tr_references);

        return strval($table);
    }
}

class TestcaseParserStates {
    public static $INIT_STATE=1, $PRECONDITION_STATE=2, $PURPOSE_STATE=3, $REFERENCES_STATE=4;
}

function parseLine($line, $state, $test_case) {
    switch ($state) {
        case TestcaseParserStates::$INIT_STATE:
            if (preg_match("/Precondition/i", $line)) {
                $state = TestcaseParserStates::$PRECONDITION_STATE;
            }
            break;

        case TestcaseParserStates::$PRECONDITION_STATE:
            if (preg_match("/Purpose/i", $line)) {
                $state = TestcaseParserStates::$PURPOSE_STATE;
            } else {
                $test_case->precondition .= $line;
            }
            break;

        case TestcaseParserStates::$PURPOSE_STATE:
            if (preg_match("/References/i", $line)) {
                $state = TestcaseParserStates::$REFERENCES_STATE;
            } else {
                $test_case->purpose .= $line;
            }
            break;
        case TestcaseParserStates::$REFERENCES_STATE:
            $test_case->references .= $line;
            break;
    }
    
    return $state;
}

function loadTestCase ($id, $filename) {
    $handle = fopen($filename, "r");
    
    if (!$handle) {
        throw new RuntimeException("test case $filename could not be opened!");
    }
    
    $test_case = new Testcase();
    $test_case->id = $id;
    
    $state = TestcaseParserStates::$INIT_STATE;
    
    while (($raw_line = fgets($handle)) != null) {
        $line = trim($raw_line);
        if ($line === "") {
            continue;
        }
        
        $state = parseLine($raw_line, $state, $test_case);
    }
    
    fclose($handle);
    
    return $test_case;
}

function loadTestCases($suite_folder_name) {
    $test_cases = scandir($suite_folder_name);
    $test_cases_filtered = array_filter($test_cases, function($element) {
        return !($element === "." || $element === "..");
    });
    
    $ret = array();
    
    foreach ($test_cases_filtered as $test_case_id) {
        $filename = $suite_folder_name . "/" . $test_case_id;
        $test_case = loadTestCase($test_case_id, $filename);
        array_push($ret, $test_case);
    }
    
    return $ret;
}

$test_suites = array(
    new Testsuite("nftsp", "negotiation-forward-tsn-supported-parameter", "Negotiation of Forward-TSN-supported parameter"),
    new Testsuite("ssi", "sender-side-implementation", "Sender Side Implementation", 'These test cases use the term "abandoned" like defined in <a href="https://tools.ietf.org/html/rfc3758#section-3.4">RFC 3758 [section 3.4]</a>. 
                                    This means that these test cases have to be implemented for each specific policy rule that defines when a data chunk should be considered "abandoned" for the sender.')
);

$all_test_cases = array();

foreach ($test_suites as $test_suite) {
    $test_suite->test_cases = loadTestCases($test_suite->folderName);
}


?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test-Suite for the SCTP Partial Reliability Extension</title>
	<style type="text/css">
		* {
			padding: 0;
			margin: 0;
		}
		body {
			margin: 10px 100px 10px 20px;
		}
		h1 {
			margin: 20px 0px;
		}
		h2 {
			margin: 15px 10px;
		}
		h3 {
			margin: 12px 15px;
		}
		ul {
			padding: 2em;
   			list-style-type: disc;
   			list-style-position: outside;
   			list-style-image: none;
		}
	
		.overview_ol {
			list-style-type: upper-roman; 
			padding: 1em 2em;
		}
		
		ol {
			padding: 1em 2em;
		}
	
		#abbreviation_table {
			border-collapse: collapse;
		}

		#abbreviation_table td {
			border: 1px solid black;
			padding: 10px;
		}
	
		.test_case_table {
			width: 75%;
			border-collapse: collapse;
			margin: 20px 20px;
			background-color: #ececec;
		}
	
		.test_case_table td {
			border: 1px solid black;
			padding: 10px;
		}
			
		.todo {
			color: red;
		}
	
		a {
			color: blue;
		}
	</style>
  </head>
  <body>
	<h1>Introduction</h1>
	<p>This is a html document that describes the test suite for the partial reliability extension of sctp.
	   It is current work in progress and later this introduction will be a general description of the 
       designed test suite.
	</p>
	<h2>Abbreviations</h2>
        <?php
        $abbrev_table = HtmlNode::get_builder("table")->attribute("id", "abbreviation_table")->build();
        $abbrev_table_tbody = HtmlNode::get_builder("tbody")->build();
        $abbrev_table->addChildNode($abbrev_table_tbody);
        
        foreach ($abbreviations as $abbreviation) {
            $tr = HtmlNode::get_builder("tr")->build();
            $td1 = HtmlNode::get_builder("td")->s_text($abbreviation->abbreviation)->build();
            $td2 = HtmlNode::get_builder("td")->s_text($abbreviation->long_description)->build();
            $tr->addChildNode($td1);
            $tr->addChildNode($td2);
            $abbrev_table_tbody->addChildNode($tr);
        }
        
        echo $abbrev_table;
        
        ?>
	
	<h2>External references</h2>
	This testsuite is based upen the following documents:
        
        <?php
            $ul = HtmlNode::get_builder("ul")->build();
            
            foreach ($external_references as $external_reference) {
                $li = HtmlNode::get_builder("li")->build();
                $a = HtmlNode::get_builder("a")->attribute("id", $external_reference->id)
                        ->attribute("href", $external_reference->link)->s_attribute("title", $external_reference->name)
                        ->s_text($external_reference->name)->build();
                $li->addChildNode($a);
                $ul->addChildNode($li);
            }
            
            echo $ul;
        ?>
	
	<h1>Overview of Test-Suite-Structure</h2>
	<ol class="overview_ol">
		<li>Negotiation of Forward-TSN-supported parameter</li>
		<li>Sender Side Implementation</li>
		<li>Receiver Side Implementation</li>
		<li>Additional Policies</li>
		<li>Corner cases and error conditions</li>
	</ol>
	
	<h1>Definition of Test-Suite</h1>
        
	<?php
		foreach ($test_suites as $test_suite) {
			echo $test_suite;
		}
	?>
  </body>
</html>
