@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
    <tr>
        <td align="{{ $align }}">
            <table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
                <tr>
                    <td align="{{ $align }}">
                        <table border="0" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                                <td align="center">
                                    <!--[if mso]>
                                    <v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:55px;v-text-anchor:middle;width:250px;" arcsize="22%" stroke="f" fillcolor="#E8A33D">
                                        <w:anchorlock/>
                                        <center style="color:#0A1E33;font-family:sans-serif;font-size:14px;font-weight:bold;text-transform:uppercase;letter-spacing:1px;">{!! $slot !!}</center>
                                    </v:roundrect>
                                    <![endif]-->
                                    
                                    <!-- Tombol Utama untuk Browser & Mobile -->
                                    <a href="{{ $url }}" 
                                       class="button" 
                                       target="_blank" 
                                       rel="noopener"
                                       style="background-color: #E8A33D; 
                                              border: 1px solid #E8A33D; 
                                              border-radius: 12px; 
                                              color: #0A1E33 !important; 
                                              display: inline-block; 
                                              font-family: 'Plus Jakarta Sans', 'Helvetica Neue', Helvetica, Arial, sans-serif; 
                                              font-size: 13px; 
                                              font-weight: 800; 
                                              line-height: 1; 
                                              padding: 20px 40px; 
                                              text-align: center; 
                                              text-decoration: none; 
                                              text-transform: uppercase; 
                                              letter-spacing: 2px; 
                                              box-shadow: 0 4px 10px rgba(232, 163, 61, 0.3);
                                              -webkit-text-size-adjust: none;
                                              mso-hide: all;">
                                        {!! $slot !!}
                                    </a>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>