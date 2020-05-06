<?php

/**
 * Parse the page data to find the score for security headers
 *
 * @return array An array with security headers list and a total score or empty array
 */
function getSecurityHeadersInfo($testInfo, $testRunResults) {
  $testResults = $testRunResults->getstepResult(1);
  if ($testResults) {
    $securityHeaders = $testResults->getRawResults()['securityHeaders'];
  }

  return $securityHeaders;
}

/**
 * Parse the page data to find any JavaScript vulnerabilities detected
 * in libraries on the page
 *
 * @return boolean A array of vulnerabilities count detected by their severity
 */
function getSecurityVulnerabilitiesBySeverity($testInfo, $testRunResults) {
  $testResults = $testRunResults->getstepResult(1);
  $severityDetected['high'] = 0;
  $severityDetected['medium'] = 0;
  $severityDetected['low'] = 0;
  if ($testResults) {
    $jsLibsVulns = $testResults->getRawResults()['jsLibsVulns'];
    foreach ($jsLibsVulns as $vuln) {
      $severityDetected[$vuln['severity']]++;
    }
  }


  return $severityDetected;
}

/**
 * Gets the overall security score's grade
 */
function getSecurityGrade($testInfo, $testRunResults) {
  $securityHeadersData = getSecurityHeadersInfo($testInfo, $testRunResults);

  $gradeResult = array();
  if ($securityHeadersData) {
    $securityHeadersScore = $securityHeadersData['securityHeadersScore'];
  }

  $scoreFactorDown = 0;
  $securityVulns = getSecurityVulnerabilitiesBySeverity($testInfo, $testRunResults);
  if (isset($securityVulns['high']) && $securityVulns['high'] >= 1) {
    if ($securityHeadersScore >= 75) {
      $securityHeadersScore = 70;
    }
  } else {
    if (isset($securityVulns['medium'])) {
      $mediumScoreFactor = 25;
      $scoreFactorDown += ($securityVulns['medium']*$mediumScoreFactor);
    }

    if (isset($securityVulns['low'])) {
      $lowScoreFactor = 20;
      $scoreFactorDown += ($securityVulns['low']*$lowScoreFactor);
    }
  }

  $overallScore = $securityHeadersScore;
  if ($overallScore >= 75) {
    $overallScore = ($securityHeadersScore-$scoreFactorDown);
  }

  if ($overallScore >= 95)
    $grade = 'A+';
  elseif ($overallScore >= 75)
    $grade = 'A';
  elseif ($overallScore >= 60)
    $grade = 'B';
  elseif ($overallScore >= 50)
    $grade = 'C';
  elseif ($overallScore >= 29)
    $grade = 'D';
  elseif ($overallScore >= 14)
    $grade = 'E';
  elseif ($overallScore >= 0)
    $grade = 'F';
  else
    $grade = 0;

  $gradeResult['class'] = substr($grade, 0, 1);
  if ($gradeResult['class'] === 'E') {
    $gradeResult['class'] = 'F';
  }

  $gradeResult['grade'] = $grade;
  $gradeResult['description'] = 'Security score';

  return $gradeResult;
}