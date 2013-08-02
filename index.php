<!DOCTYPE html>
<html>
  <head>
    <title>iQUEST</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap -->
    <link href="css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="css/custom.css" rel="stylesheet" media="screen">
    <script type="text/javascript" src="http://www.google.com/jsapi"></script>
    <script type="text/javascript">
      google.load('visualization', '1', {packages: ['corechart']});
    </script>

  </head>
  <body>
  <body>


    <div class="container">

      <div class="panel">
        <h1>Map Here</h1>
      </div>

      <div class="body-content">

        <div class="row">
          
          <form class="form-inline">
                <fieldset>
                  <div class="form-group">
                    <label for="start_date">Starting Date</label>
                    <input type="text" class="form-control" id="start_date" placeholder="Starting Date">
                  </div>

                  <div class="form-group">
                    <label for="end_date">Ending Date</label>
                    <input type="text" class="form-control" id="end_date" placeholder="Ending Date">
                  </div>

                  <button type="submit" class="btn btn-default">Submit</button>
                </fieldset>
              </form>

          <ul class="nav nav-tabs">
            <li>
              <a data-toggle="tab" href="#color">
                  <h4>Water Color</h4>
                  <p>Clear<span class="pull-right">###</span></p>
                  <p>Discolored<span class="pull-right">###</span></p>

                </a>
            </li>
            
            <li>
              <a data-toggle="tab" href="#taste">
                 <h4>Taste / Odor</h4>
                  <p>Good<span class="pull-right">###</span></p>
                  <p>Bad<span class="pull-right">###</span></p>              
              </a>
            </li>
            
            <li>
              <a data-toggle="tab" href="#particles">
                  <h4>Particles</h4>
                  <p>None<span class="pull-right">###</span></p>
                  <p>Some<span class="pull-right">###</span></p>
              </a>
            </li>
            <li>
              <a data-toggle="tab" href="#flow">
                  <h4>Hours of Flow</h4>
                  <p>12<span class="pull-right">###</span></p>
                  <p>4-12<span class="pull-right">###</span></p>
                  <p>3<span class="pull-right">###</span></p>
                  <p>None<span class="pull-right">###</span></p>
              </a>
              </li>
          </ul>

          <div id="charts" class="tab-content">
            <div class="tab-pane" id="color"></div>
            <div class="tab-pane" id="taste"></div>
            <div class="tab-pane" id="particles"></div>
            <div class="tab-pane" id="flow"></div>
          </div>
        </div>

        <hr>

        <footer>
        </footer>
      </div>

    </div> <!-- /container -->

  
</body>    <!-- JavaScript plugins (requires jQuery) -->
    <script src="http://code.jquery.com/jquery.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="js/bootstrap.min.js"></script>
    <script src="js/iquest.js"></script>

  </body>
</html>