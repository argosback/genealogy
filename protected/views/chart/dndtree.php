<!DOCTYPE html>
<meta charset="utf-8">
<style type="text/css">
  
	.node {
    cursor: pointer;
  }

  .overlay{
      background-color:#EEE;
  }
   
  .node circle {
    fill: #fff;
    stroke: steelblue;
    stroke-width: 1.5px;
  }
   
  .node text {
    font-size:10px; 
    font-family:sans-serif;
  }
   
  .link {
    fill: none;
    stroke: #ccc;
    stroke-width: 1.5px;
  }

  .templink {
    fill: none;
    stroke: red;
    stroke-width: 3px;
  }

  .ghostCircle.show{
      display:block;
  }

  .ghostCircle, .activeDrag .ghostCircle{
       display: none;
  }

</style>
<script type="text/javascript">
var jsonurl = '<?=$jsonurl?>';
</script>
<script src="http://code.jquery.com/jquery-1.10.2.min.js"></script>
<script src="http://d3js.org/d3.v3.js"></script>
<script src="/js/dndtree.js"></script>
<body>
<p>
<?php
echo $mother . " " . $father;
?>
</p>

    <div id="tree-container"></div>
</body>
</html>