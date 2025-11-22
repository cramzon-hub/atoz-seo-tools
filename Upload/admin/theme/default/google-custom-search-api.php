<?php
defined('APP_NAME') or die(header('HTTP/1.1 403 Forbidden'));

/*
 * @author Balaji
 * @name: Rainbow PHP Framework
 * @copyright 2020 ProThemes.Biz
 *
 */
?>
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        <?php echo $pageTitle; ?>  
        <small>Control panel</small>
      </h1>
      <ol class="breadcrumb">
        <li><a href="<?php adminLink(); ?>"><i class="<?php getAdminMenuIcon($controller,$menuBarLinks); ?>"></i> Admin</a></li>
        <li class="active"><a href="<?php adminLink($controller); ?>"><?php echo $pageTitle; ?></a> </li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

          <div class="box box-primary">
            <div class="box-header with-border">
              <h3 class="box-title"><?php echo $subTitle; ?></h3>
              <div style="position:absolute; top:4px; right:15px;">
                <?php if($pointOut == ''){ ?>
                <a href="<?php adminLink($controller.'/add-key'); ?>" class="btn btn-danger"><i class="fa fa-fw fa-plus"></i> Add API Key</a>
                <?php }else{ ?>
                    <a href="https://developers.google.com/custom-search/v1/overview" target="_blank" rel="nofollow" class="btn btn-xs btn-success"> Where to get API Key?</a>
                <?php } ?>

              </div>
            </div><!-- /.box-header ba-la-ji -->
            <div class="box-body">
            <?php if(isset($msg)) echo $msg; ?><br />
            
            <?php if($pointOut == 'add-key'){ ?>

            <div class="row" style="padding-left: 10px; padding-right: 10px;">
                <form action="#" method="POST">
                    <div class="box-body">

                        <div class="form-group">
                            <label>API Key</label>
                            <input required="" placeholder="Enter your API key" name="api" value="" class="form-control" type="text">
                        </div>

                        <div class="form-group">
                            <label>Total Allowed Credits</label>
                            <input required="" placeholder="Enter your API key" name="total" value="100" class="form-control" type="number">
                        </div>

                        <input class="btn btn-success" value="Add Key" type="submit">
                        <a class="btn btn-danger" href="<?php adminLink($controller); ?>">Cancel</a>
                        <br>

                    </div><!-- /.box-body -->
                </form>
            </div>        
            
            <?php } else { ?>
            <table cellpadding="0" cellspacing="0" border="0" class="table table-striped table-bordered" id="mySitesTable">
            	<thead>
            		<tr>
                          <th>API Key</th>
                          <th>Total Credits</th>
                          <th>Remaining Credits</th>
                          <th>Actions</th>
            		</tr>
            	</thead>         
                <tbody>                        
                </tbody>
            </table>
            <?php } ?>

            <br />
            
            </div><!-- /.box-body -->
          </div><!-- /.box -->
  
    </section><!-- /.content -->
  </div><!-- /.content-wrapper -->
<?php 
$ajaxLink = adminLink('?route='.$controller.'/'.'api-keys',true);
$footerAddArr[] = <<<EOD
    <script type="text/javascript" language="javascript" class="init">
    $(document).ready(function() {
    	$('#mySitesTable').dataTable( {
    		"processing": true,
    		"serverSide": true,
    		"ajax": "$ajaxLink"
    	} );
    } );
    </script>    
EOD;
?>