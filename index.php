<!DOCTYPE html>
<html>
  <head>
    <title>iQUEST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/iquest.css" rel="stylesheet" media="screen">
    <link href="css/datepicker.css" rel="stylesheet" media="screen">
    <link href="css/bootstrap-glyphicons.css" rel="stylesheet">
    <script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="js/moment.min.js"></script>

    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/bootstrap-datepicker.js"></script>

    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script src="http://maps.google.com/maps/api/js?sensor=false&v=3.2"></script>

    <script type='text/javascript' src='OpenLayers.js'></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>
    <script src="js/iquest.js"></script>

  </head>
  <body onload="loadEverything();">
   

  <div class="navbar navbar-inverse">
        <div class="container">
          <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <a class="navbar-brand" href="#">iQUEST</a>

            <ul class="nav navbar-nav pull-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">View <b class="caret"></b></a>
                 <ul class="dropdown-menu">
                    <li id="all_link"><a href="#" onclick="showAll()">All</a></li>
                    <li id="neighborhood_link" class="active"><a href="#" onclick="showNeighborhood()">Neighborhood</a></li>
                  </ul>
              </li>
            </ul>

            <ul class="nav navbar-nav pull-right">
              <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Neighborhoods <b class="caret"></b></a>
                 <ul class="dropdown-menu neighborhoods">
                  </ul>
              </li>
            </ul>

          </div><!-- /.container -->
  </div><!-- /.nav-bar -->
      
        <div class="container" id="view_neighborhood">
            
            <div class="row">
              <div id="contentMap" class="col-lg-12 row"></div>
            </div>
            <div class="row">
              <h4 id="neighborhood"></h4>
            </div>

            <div class="row" id="infoBox">
              <div class="col-lg-3">
                <dl class="dl-horizontal">
                  <dt>Customers</dt>
                  <dd id="customers">200</dd>
                  <dt>SMS Reports</dt>
                  <dd id="reports">200</dd>
                  <dt>Rating</dt>
                  <dd><div class='progress'><div class='progress-bar progress-bar-success' id="index_rating" style='width: 40%'></div></div></dd>
                </dl>
              </div>
            </div>
            
            <div class="row">
              <div class="navbar">
                <div class="container">

                  <ul class="nav navbar-nav">
    <!--                <li class="dateRangeSelector"><a href="#">Today</a></li> -->
                    <li class="dateRangeSelector"><a href="#">7 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">14 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">30 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">3 Months</a></li>
                    <li class="dateRangeSelector active"><a href="#">1 Year</a></li>
                    <li class="dateRangeSelector"><a href="#">Custom</a></li>
                  </ul>
                  
                  <ul class="nav navbar-nav pull-right">                
                    <li>
                      <div class="input-group" id="custom_date">
                        <form class="navbar-form pull-right">
                            <input type="text" class="span2" value="2000-01-31" data-date-format="yyyy-mm-dd" id="dpStart" class="date"> to 
                            <input type="text" class="span2" value="2015-01-31" data-date-format="yyyy-mm-dd" id="dpEnd" class="date">
                            <button type="button" class="btn btn-primary btn-small" onClick="populateCharts()">Go</button>
                        </form>
                      </div>
                    </li>
                  </ul>
                </div> <!--container-->
              </div>  <!--navbar-->         
            </div> <!-- row -->
            
            <div class="row" id="chartOptions">
              <ul class="nav nav-tabs" id="chartTabs">
                <li>
                  <a data-toggle="tab" href="#water">
                      <h4>Water Color</h4>
                    </a>
                </li>
                
                <li>
                  <a data-toggle="tab" href="#taste">
                     <h4>Taste / Odor</h4>
                  </a>
                </li>
                
                <li>
                  <a data-toggle="tab" href="#particles">
                      <h4>Particles</h4>
                  </a>
                </li>
                <li>
                  <a data-toggle="tab" href="#flow">
                      <h4>Hours of Flow</h4>
                  </a>
                  </li>
              </ul>

              <div id="charts" class="tab-content">
                
                <div class="tab-pane" id="water">
                  <div id="water_line">No data available, try another date range</div>
                  <div id="water_bar"></div>
                </div>
                
                <div class="tab-pane" id="taste">
                  <div id="taste_line">No data available, try another date range</div>
                  <div id="taste_bar"></div>
                </div>
                
                <div class="tab-pane" id="particles">
                  <div id="particles_line">No data available, try another date range</div>
                  <div id="particles_bar"></div>
                </div>
                
                <div class="tab-pane" id="flow">
                  <div id="flow_line">No data available, try another date range</div>
                  <div id="flow_bar"></div>
                </div>
              
              </div>
            
            </div> <!-- row chart options -->
    </div>



        <div class="container" id="view_all">
            <h4>All</h4>
            <div class="row">
              <div class="navbar">
                <div class="container">

                  <ul class="nav navbar-nav">
    <!--                <li class="dateRangeSelector"><a href="#">Today</a></li> -->
                    <li class="dateRangeSelector active"><a href="#">7 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">14 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">30 Days</a></li>
                    <li class="dateRangeSelector"><a href="#">3 Months</a></li>
                    <li class="dateRangeSelector"><a href="#">1 Year</a></li>
                    <li class="dateRangeSelector"><a href="#">Custom</a></li>
                  </ul>
                  
                  <ul class="nav navbar-nav pull-right">                
                    <li>
                      <div class="input-group" id="custom_date_all">
                        <form class="navbar-form pull-right">
                            <input type="text" class="span2" value="2000-01-31" data-date-format="yyyy-mm-dd" id="dpStart2" class="date"> to 
                            <input type="text" class="span2" value="2015-01-31" data-date-format="yyyy-mm-dd" id="dpEnd2" class="date">
                            <button type="button" class="btn btn-primary btn-small" onClick="populateAllCharts()">Go</button>
                        </form>
                      </div>
                    </li>
                  </ul>
                </div> <!--container-->
              </div>  <!--navbar-->         
            </div> <!-- row -->
            
            <div class="row" id="chartOptions">
              <ul class="nav nav-tabs" id="chartTabsAll">
                <li>
                  <a data-toggle="tab" href="#overview">
                      <h4>Overview</h4>
                    </a>
                </li>
                
                <li>
                  <a data-toggle="tab" href="#index">
                     <h4>Index Ratings</h4>
                  </a>
                </li>
                
              </ul>

              <div id="chartsAll" class="tab-content">
                
                <div class="tab-pane" id="overview">
                  <div id="overview_line">No data available, try another date range</div>
                </div>
                
                <div class="tab-pane" id="index">
                  <div id="index_line">No data available, try another date range</div>
                </div>
                              
              </div>
            
            </div> <!-- row chart options -->
    </div>






    <footer>
    </footer>
  
</body>    <!-- JavaScript plugins (requires jQuery) -->
</html>