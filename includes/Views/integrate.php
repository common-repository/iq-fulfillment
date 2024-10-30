<div class="container">
    <div class="content">
        <h1>Lets Get You To Integrated With <strong class="iq-name">IQ Fulfillment</strong></h1>
        <form action="" method="post">
            <?php wp_nonce_field('try-integration') ?>
            <?php submit_button('Click Here', 'gradient-button', 'submit_integration_request', true, null); ?>
        </form>
    </div>
</div>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;700&display=swap');

    .container {
        margin: 20px;
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        height: 90vh;
        border-radius: 15px;
        background: linear-gradient(to right, #9900ff, #d40082);
    }
    .content{
        display: flex;
        justify-content: center;
        align-items: center;
        flex-direction: column;
        text-align: center;
        width: 99%;
        height: 98%;
        background: #ffffff;
        border-radius: 15px;
    }

    .gradient-button {
        background: linear-gradient(to right, #9900ff, #d40082) !important;
        color: white !important;
        padding: 20px 50px !important;
        border-radius: 5px !important;
        text-align: center !important;
        text-decoration: none !important;
        display: inline-block !important;
        font-family: "Space Grotesk" !important;
        font-size: 20px !important;
    }
    h1{
        font-family: "Space Grotesk";
        font-weight: 300;
        font-size: 56px;
        margin-bottom: 40px;
    }
    .iq-name{
        font-weight: bold;
        font-weight: 700;
    }
</style>

