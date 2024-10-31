<?php 
/* *
 * Plugin Name: Recibir mensajes instantáneos
 * Plugin URI: https://www.webycomunicacion.es/plugins/chat-mensajes-instantaneos
 * Description: Añade botón flotante para que los visitantes puedan contactar directamente vía WhatsApp.
 * Version: 2.3
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author: J&L | Web y Comunicación
 * Author URI: https://www.webycomunicacion.es
 * Text Domain: jlwebcom-chatwsp
 * Domain Path: /languages/
 * License: GPLv2 
 * */ 

wp_enqueue_style( 'style', esc_url( plugins_url( 'asserts/css/style.css', __FILE__ ) ) );

add_action( 'wp_footer', 'jlwebcom_chatwsp_mostrar_boton' );
function jlwebcom_chatwsp_mostrar_boton() {
    $telefono = get_option( 'jlwebcom_chatwsp_telefono' );
    $mensaje = get_option( 'jlwebcom_chatwsp_mensaje' );
    if( $telefono != '' ){
    ?>
    <div class="jlwebcom_chatwsp">
        <ul>
            <a href="https://api.whatsapp.com/send?phone=<?php printf( __( '%s', 'jlwebcom-chatwsp' ), $telefono ); ?>&amp;text=<?php printf( __( '%s', 'jlwebcom-chatwsp' ), $mensaje ); ?>" target="_blank" rel="noopener"><li class="jlwebcom_chatwsp color-wsp"><img data-src="<?php echo esc_url( plugins_url( 'asserts/img/icon-chat-verde.png', __FILE__ ) ); ?>" class="jlwebcom_chatwsp lazyloaded" src="<?php echo esc_url( plugins_url( 'asserts/img/icon-chat-verde.png', __FILE__ ) ); ?>"><noscript><img class="jlwebcom_chatwsp" src="<?php echo esc_url( plugins_url( 'asserts/img/icon-chat-verde.png', __FILE__ ) ); ?>"></noscript></li></a>
        </ul>
    </div>
    <?php
    }
}

//Crea variables de opción con valor predeterminado
register_activation_hook( __FILE__, 'jlwebcom_chatwsp_default_options');
function jlwebcom_chatwsp_default_options(){
    if( get_option( 'jlwebcom_chatwsp_telefono' ) === false ){
        add_option( 'jlwebcom_chatwsp_telefono', '' );
    }
    if( get_option( 'jlwebcom_chatwsp_mensaje' ) === false ){
        add_option( 'jlwebcom_chatwsp_mensaje', '' );
    }
}

//Añade página al menú de configuración
add_action( 'admin_menu', 'jlwebcom_chatwsp_ajustes' );
function jlwebcom_chatwsp_ajustes(){
    $pagina_opciones = add_options_page( 'Chat de WhatsApp',                //Título de la página
                                        'Configurar chat',                  //Nombre en menú
                                        'manage_options',                   //Nivel de acceso (solo usuarios)
                                        'jlwebcom_chatwsp_conf',            //Slug
                                        'jlwebcom_chatwsp_genera_pagina'    //Función gestora de página
                                        );
}

//Genera el código de la página de ajustes
function jlwebcom_chatwsp_genera_pagina(){
    $telefono   = get_option( 'jlwebcom_chatwsp_telefono' );
    $mensaje    = get_option( 'jlwebcom_chatwsp_mensaje' );

    ?>
    <div class="wrap">
        <h2><?php printf( __( 'Chat de WhatsApp', 'jlwebcom-chatwsp' ) ); ?></h2>
    </div>

    <form method="post" action="admin-post.php">
        <input type="hidden" name="action" value="jlwebcom_chatwsp_guardar">
        <?php wp_nonce_field('jlwebcom_chatwsp_token') ?>
        <br/>
        <?php printf( __( 'Teléfono', 'jlwebcom-chatwsp' ) ); ?>
        <input type="text" name="jlwebcom_chatwsp_telefono" value="<?php printf( __( '%s', 'jlwebcom-chatwsp' ), $telefono ); ?>" placeholder="34XXXXXXXXX"/>
        <br/><br/>
        <?php printf( __( 'Mensaje:', 'jlwebcom-chatwsp' ) ); ?>
        <input type="text" name="jlwebcom_chatwsp_mensaje" value="<?php printf( __( '%s', 'jlwebcom-chatwsp' ), $mensaje ); ?>"/>
        <br/><br/>
        <input type="submit" value="<?php printf( __( 'Guardar', 'jlwebcom-chatwsp' ) ); ?>" clss="button-primary"/>
    </form>
    <?php     
}

//Guardar datos de formulario
add_action( 'admin_post_jlwebcom_chatwsp_guardar', 'jlwebcom_chatwsp_guardar' );
function jlwebcom_chatwsp_guardar(){

    //Validar permisos de usuario
    if( !current_user_can( 'manage_options' ) ){
        wp_die( 'Not allowed' );
    }

    //Validar token
    check_admin_referer( 'jlwebcom_chatwsp_token' );

    //Limpiar valores
    $telefono = sanitize_text_field( $_POST['jlwebcom_chatwsp_telefono'] );
    $mensaje  = sanitize_text_field( $_POST['jlwebcom_chatwsp_mensaje'] );

    //Actualizar valores
    update_option( 'jlwebcom_chatwsp_telefono', $telefono );
    update_option( 'jlwebcom_chatwsp_mensaje', $mensaje );

    //Regreso a página de ajustes
    wp_redirect( add_query_arg( 'page',
                                'jlwebcom_chatwsp_conf',
                                admin_url( 'options-general.php' )
                              ) 
                );
    exit;
}