<?php

date_default_timezone_set('UTC');

define('NAME', 'ushis2quakeml');
define('VERSION', '0.1');


/**
 * Format an integer as a roman numeral.
 */
function romanNumeral($num) {
	static $romans = array(
		0 => 'I',
		1 => 'I',
		2 => 'II',
		3 => 'III',
		4 => 'IV',
		5 => 'V',
		6 => 'VI',
		7 => 'VII',
		8 => 'VIII',
		9 => 'IX',
		10 => 'X',
		11 => 'XI',
		12 => 'XII');

	return $romans[$num];
}

function sortMagnitudesPreferredFirst($a, $b) {
	$aType = strtoupper($a['type']);
	$bType = strtoupper($b['type']);
	$aMag = $a['magnitude'];
	$bMag = $b['magnitude'];

	// largest Mw
	$aMw = ($aType === 'MW');
	$bMw = ($bType === 'MW');
	if ($aMw && !$bMw) {
		return -1;
	} else if ($bMw && !$aMw) {
		return 1;
	} else if ($aMw && $bMw) {
		return $bMag - $aMag;
	}

	// largest Mb or Ms
	$aMsMb = ($aType === 'MB' || $aType === 'MS');
	$bMsMb = ($bType === 'MB' || $bType === 'MS');
	if ($aMsMb && !$bMsMb) {
		return -1;
	} else if ($bMsMb && !$aMsMb) {
		return 1;
	} else if ($aMsMb && $bMsMb) {
		return $bMag - $aMag;
	}

	// move "felt area" mags to end
	if ($aType === 'FA' && $bType !== 'FA') {
		return 1;
	} else if ($bType === 'FA' && $aType !== 'FA') {
		return -1;
	}

	// largest first
	return $bMag - $aMag;
}

function getQuakeml($line) {
	static $eventCodeSequence = 1;
	static $dateFormat = 'Y-m-d\TH:i:s\Z';

	$dataSource = 'us';
	$eventSource = 'ushis';
	$eventCode = $eventCodeSequence++;
	$eventId = $eventSource . $eventCode;

	$publicIDBase = 'quakeml:' . $dataSource . '.anss.org';
	$eventParametersPublicID = $publicIDBase . '/eventParameters/' . $eventId . '/' . microtime(true);
	$eventPublicID = $publicIDBase . '/event/' . $eventId;
	$originPublicID = $publicIDBase . '/origin/' . $eventId;
	$magnitudePublicIDBase = $publicIDBase . '/magnitude/' . $eventId;

	$year = $line['year'];
	$month = $line['month'];
	$day = $line['day'];
	$hour = $line['hour'];
	$minute = $line['minute'];
	$second = $line['second'];
	
	$eventTime = sprintf('%\'04d-%\'02d-%\'02dT%\'02d:%\'02d:%\'06.3fZ',
			intval($year),
			intval($month),
			intval($day),
			intval($hour),
			intval($minute),
			floatval($second));
	$latitude = $line['latitude'];
	$longitude = $line['longitude'];
	$depth = $line['depth'];
	$constraint = $line['epicenter constraint'];
	$feregion = $line['FE  region'];
	$precision = $line['precision'];
	$mmi = $line['MMI'];
	$felt = $line['felt'];

	$originType = ($constraint === 'Z' ? 'macroseismic' : 'hypocenter');

	$magnitudes = array();
	if ($line['mag_1'] !== '') {
		$magnitudes[] = array(
			'magnitude' => $line['mag_1'],
			'source' => $line['mag_source_1'],
			'type' => $line['mag_type_1'],
			'id' => $magnitudePublicIDBase . '/mag_1');
	}
	if ($line['mag_2'] !== '') {
		$magnitudes[] = array(
			'magnitude' => $line['mag_2'],
			'source' => $line['mag_source_2'],
			'type' => $line['mag_type_2'],
			'id' => $magnitudePublicIDBase . '/mag_2');
	}
	if ($line['mb'] !== '') {
		$magnitudes[] = array(
			'magnitude' => $line['mb'],
			'source' => 'us',
			'type' => 'mb',
			'id' => $magnitudePublicIDBase . '/mb');
	}
	if ($line['Ms'] !== '') {
		$magnitudes[] = array(
			'magnitude' => $line['Ms'],
			'source' => 'us',
			'type' => 'Ms',
			'id' => $magnitudePublicIDBase . '/Ms');
	}
	usort($magnitudes, "sortMagnitudesPreferredFirst");

	$q = '<?xml version="1.0" ?>
<q:quakeml xmlns="http://quakeml.org/xmlns/bed/1.2"' .
		' xmlns:catalog="http://anss.org/xmlns/catalog/0.1"' .
		' xmlns:q="http://quakeml.org/xmlns/quakeml/1.2"' .
		' xmlns:anss="http://anss.org/xmlns/event/0.1"' .
		'>
	<eventParameters publicID="' . $eventParametersPublicID . '">
		<comment><text>' . NAME . ' ' . VERSION . '</text></comment>
		<event publicID="' . $eventPublicID . '"' .
				' catalog:datasource="' . $dataSource . '"' .
				' catalog:eventsource="' . $eventSource . '"' .
				' catalog:eventid="' . $eventCode . '"' .
				'>';

	if ($mmi) {
		$q .= '
			<description>
				<type>felt report</type>
				<text>Maximum observed Modified Mercalli Intensity (MMI) ' .
					romanNumeral(intval($mmi)) . '</text>
			</description>';
	}

	$q .= '
			<preferredOriginID>' . $originPublicID . '</preferredOriginID>
			<origin publicID="' . $originPublicID . '">
				<latitude><value>' . $latitude . '</value></latitude>
				<longitude><value>' . $longitude . '</value></longitude>';
	if ($depth) {
		$q .= '
				<depth><value>' . $depth * 1000 . '</value></depth>';
	}

	$q .= '
				<time><value>' . $eventTime . '</value></time>
				<compositeTime>
					<year>' . $year . '</year>
					<month>' . $month . '</month>
					<day>' . $day . '</day>';

	if ($hour !== '') {
		$q .= '
					<hour>' . $hour . '</hour>';
		if ($minute !== '' && $minute !== '0') {
			$q .= '
					<minute>' . $minute . '</minute>';
			if ($second !== '' && $second !== '0') {
				$q .= '
					<second>' . $second . '</second>';
			}
		}
	}
	$q .= '
				</compositeTime>';

	$q .= '
				<type>' . $originType . '</type>
				<evaluationMode>manual</evaluationMode>
				<evaluationStatus>final</evaluationStatus>
			</origin>';

	$preferredMagnitudeID = null;
	foreach ($magnitudes as $mag) {
		if ($preferredMagnitudeID === null) {
			$preferredMagnitudeID = $mag['id'];
			$q .= '
			<preferredMagnitudeID>' . $preferredMagnitudeID . '</preferredMagnitudeID>';
		}

		$q .= '
			<magnitude publicID="' . $mag['id'] . '">
				<mag><value>' . $mag['magnitude'] . '</value></mag>
				<type>' . $mag['type'] . '</type>
				<evaluationMode>manual</evaluationMode>
				<evaluationStatus>final</evaluationStatus>
				<creationInfo>
					<agencyID>' . $mag['source'] . '</agencyID>
				</creationInfo>
			</magnitude>';
	}

		$q .= '
			<type>earthquake</type>
			<creationInfo>
				<agencyID>ushis</agencyID>
				<author>Stover and Coffman (1993)</author>
			</creationInfo>
		</event>
		<creationInfo>
			<creationTime>' . str_replace('+00:00', 'Z', date('c')) . '</creationTime>
		</creationInfo>
	</eventParameters>
</q:quakeml>
';

	return array(
		'eventid' => $eventId,
		'quakeml' => $q
	);
}


mkdir('outputdir');
function processLine ($line) {
	$q = getQuakeml($line);
	file_put_contents('outputdir/' . $q['eventid'] . '.xml', $q['quakeml']);
}


// parse CSV input

$headers = null;
$stdin = fopen('php://stdin', 'r');
while ( ($line = fgets($stdin)) !== false ) {
	$line = trim($line);

	if ($headers === null) {
		$headers = str_getcsv($line);
		continue;
	}
	$line = str_getcsv($line);
	processLine(array_combine($headers, $line));
}

fclose($stdin);
