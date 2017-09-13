<!DOCTYPE HTML>
<html>
<head>
  <title>Timeline | Basic demo</title>

  <style type="text/css">
    body, html {
      font-family: sans-serif;
    }
  </style>

  <script src="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.js"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/vis/4.20.1/vis.min.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="visualization"></div>

<script type="text/javascript">
  // DOM element where the Timeline will be attached
  var container = document.getElementById('visualization');

  // Create a DataSet (allows two way data-binding)
  var items = new vis.DataSet([
<?php
include_once('tvh_class.php');
$tvh = new Tvh();
$upcomming = $tvh->getUpcomming();
$i = 1;
$groups = array();
foreach($upcomming->entries as $uc) {
  $muxArr = $tvh->channelIdToMux($uc->channel);
  $mux = empty($muxArr) ? 'none' : $muxArr[0];
  $groups[$mux] = 1;
  echo "{id: ".$i++.", group: '".$mux."', content: '".addslashes($uc->disp_title)."', start: new Date(".$uc->start."*1000), end: new Date(".$uc->stop."*1000), type: 'range' , title : '".addslashes($uc->disp_title)."'},\n";
}
/*
    {id: 1, content: 'item 1', start: '2013-04-20'},
    {id: 2, content: 'item 2', start: '2013-04-14'},
    {id: 3, content: 'item 3', start: '2013-04-18'},
    {id: 4, content: 'item 4', start: '2013-04-16', end: '2013-04-19'},
    {id: 5, content: 'item 5', start: '2013-04-25'},
    {id: 6, content: 'item 6', start: '2013-04-27'}
*/
?>
  ]);
  
  var groups = new vis.DataSet([
<?php
$i = 0;
foreach($groups as $group => $val) {
  echo "{id: '".$group."', content: '".$group."'},\n";
}
?>
  ]);
  // Configuration for the Timeline
  var options = {
    height: '400px',
    //start: '2017-09-13',
    //end: '2017-09-20'
  };

  // Create a Timeline
  //var timeline = new vis.Timeline(container, items, options);
  var timeline = new vis.Timeline(container);
  timeline.setOptions(options);
  timeline.setGroups(groups);
  timeline.setItems(items);

</script>
</body>
</html>