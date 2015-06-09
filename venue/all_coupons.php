             <div class="all_coupons">     
             <?php foreach ($coupons as $key => $value) { ?>
                       
          <div class="valid-coupon coupons col-md-3">
            <div class="coupon">
             
              <!-- Add winner option/partial -->
              <div class="flip-coupon-main">
                <div class="coupon-name"><?php if($value['pic']){ ?>
                <img src="<?php echo BASE_PATH.'timthumb.php?src=uploads/'.$value['pic']; ?>" style="width:100%; height:100%;" >
                <?php } else { ?>
                 <img src="<?php echo BASE_PATH.'timthumb.php?src=uploads/coupon-icon.jpg' ?>" style="width:100%; height:100%;" >
                 <?php } ?>
                  </div>
        <div class="active" style="position: absolute;right: 0;top: 0;font-size: x-large;">
        <?php if($value['status']) 
        {echo '<i class="fa fa-fw fa-check-square-o text-green"></i>';}
        else
       { echo '<i class="fa fa-fw fa-square-o text-red"></i>';} ?>
        </div>  
              <div class="coupon-desc"><?php echo $value['coupon_name'];  ?>  </div>
              

      <div class="">
      <div class="coupon_code"><?php echo $value['coupon_code'];  ?></div>
      </div>
      
                  <div class="col-md-12" style='padding-bottom: 8px;border-bottom: 1px solid #eaedef;'>
              <div class="col-md-6 left "> <span class="save-text">SAVE</span>
        <p class="price"><?php echo $value['value'];  ?><span class="webRupee">%</span></p>
        </div>
            
              <div class="col-md-6 right"> <span class="save-text">LIMIT</span>
        <p class="price"><?php echo $value['limit'];  ?></p>
            </div>
            
            </div> 
            <div class="info clearfix">
             <div class="edit-coupon">
              <button class="couponedit fa fa-pencil-square-o" vid="<?php echo $vid; ?>" coupon_id="<?php echo $value['id']; ?>" data-toggle="modal" data-target="#editcoupon" data-remote="true" data-no-turbolink ='true' ></button>
              <button class="coupondelete fa fa-remove" vid="<?php echo $vid; ?>" coupon_id="<?php echo $value['id']; ?>"></button>
            </div> 
      
            <p class="expiry"><span>Expiry Date</span> <br>
            <?php echo date('d-M-Y',strtotime($value['expiry_date'])); ?> 
            </p>
            <!-- </div> --><!-- pull-right -->
          </div><!-- info-clearfix -->
          

        </div> <!-- flip-coupon-main -->
      </div><!-- coupon -->
    
    </div> <!-- valid coupon -->
    <?php } ?>
    </div> 
    
   