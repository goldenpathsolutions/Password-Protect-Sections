<?php

/** 
 * Password Settings View
 * 
 * Description: View for Password Protected Sections settings page.  This file 
 * contains the layout for the Administrative Settings of this plugin.
 * 
 * @author: Patrick Jackson, Golden Path Solutions <pjackson@goldenpathsolutions.com>
 * @url: http://www.goldenpathsolutions.com
 * Created: 2014-07-21
 */



$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'instructions';


?>

<div class="gps-password-settings">
    
    <h1>Settings</h1>
    
    <div class="sidebar desktop">
        
        <div class="sidebar-content">
            <div class="sidebar-title"><h3>Brought to you by...</h3></div>
            <div class="sidebar-description center">
                <img id="gps-logo"
                     src="<?php echo plugins_url();?>/password-protect-sections/images/logo_name_c_300.png" 
                     alt="Golden Path Solutions"/>
            </div>
        </div>
        
        <div class="sidebar-content">
            <h3>Follow Us!
                <div class="social center">
                    <a href="https://twitter.com/GoldenPathSolns" 
                       title="Follow @GoldenPathSolns on Twitter">
                        <i class="fa fa-twitter-square"></i></a>
                    <a href="https://www.facebook.com/GoldenPathSolutions"
                       title="Like on Facebook">
                        <i class="fa fa-facebook-square"></i></a>
                    <a href="https://www.linkedin.com/company/golden-path-solutions-inc-"
                       title="Follow on LinkedIn">
                    <i class="fa fa-linkedin-square"></i>
                    <a href="https://plus.google.com/114562171800704044997"
                       title="Follow on Google+">
                    <i class="fa fa-google-plus-square"></i>
                </div>
            </h3>
        </div>
        
    </div>
    
    <div class="content wrap">
        
        <h2 class="nav-tab-wrapper">
            <a href="#" class="nav-tab 
                <?php echo $active_tab == 'instructions' ? 'nav-tab-active' : ''; ?>">
                Instructions
            </a>
        </h2>
        
        <div class="tab-content">
            <h2>Overview</h2>
            <p>Well, we don't really have any settings for this plugin right now, but
                here's an overview of how to use its features.</p>
        </div>
            
    </div>
    
    <div class="sidebar mobile">
        
        <div class="sidebar-content">
            <div class="sidebar-title"><h3>Brought to you by...</h3></div>
            <div class="sidebar-description center">
                <img id="gps-logo"
                     src="<?php echo plugins_url();?>/password-protect-sections/images/logo_name_c_300.png" 
                     alt="Golden Path Solutions"/>
            </div>
        </div>
        
        <div class="sidebar-content">
            <h3>Follow Us!
                <div class="social center">
                    <a href="https://twitter.com/GoldenPathSolns" 
                       title="Follow @GoldenPathSolns on Twitter">
                        <i class="fa fa-twitter-square"></i></a>
                    <a href="https://www.facebook.com/GoldenPathSolutions"
                       title="Like on Facebook">
                        <i class="fa fa-facebook-square"></i></a>
                    <a href="https://www.linkedin.com/company/golden-path-solutions-inc-"
                       title="Visit on LinkedIn">
                    <i class="fa fa-linkedin-square"></i>
                </div>
            </h3>
        </div>
        
    </div>
    
</div>