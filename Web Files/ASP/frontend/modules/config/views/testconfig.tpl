<div class="mws-panel grid_8">
    <div class="mws-panel-header">
        <span><i class="icon-list-2"></i> Preform System Tests</span>
    </div>
    <div class="mws-panel-body">
        <div class="mws-panel-content">
            <p>
                This area will allow you test the setup and configuration of your "Gamespy" database server. It is normal to sometimes see a few
                <span style="color: orange; "><b>Warn</b></span>'s, you can ignore these without too much worry. If you see any
                <span style="color: red;"><b>FAIL</b></span>'s, then you will need to go back and reconfigure your system.
                <br /><br />
                <span style="color: red; ">Note:</span> During this test, sample data will be loaded into your database.
                This will be removed after the test.
                <br /><br />
                <div style="text-align: center;">
                    <input type="button" id="test-config" class="btn btn-primary" value="Run System Tests" />
                </div>
            </p>
        </div>

        <!-- Hidden Ajax Thing -->
        <div id="ajax-dialog">
            <div class="mws-dialog-inner">
                <div style="text-align: center;">
                    <img src="/ASP/frontend/images/core/loading32.gif" />
                    <br />
                    <br />
                    Processing System Tests... Please allow up to 30 seconds for this process to complete.
                    <br />
                    <br /><span style="color: red; ">DO NOT</span> refresh this window.
                </div>
            </div>
        </div>
    </div>
</div>