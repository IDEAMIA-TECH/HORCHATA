<?php
/**
 * Yelp Integration - Integración con Yelp
 * Métodos legales para mostrar reseñas de Yelp
 */

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Integración con Yelp - Horchata Mexican Food</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-5'>
        <h1>Integración con Yelp</h1>
        <hr>
        
        <div class='alert alert-info'>
            <h5>⚠️ Importante: Consideraciones Legales</h5>
            <p>
                Extraer contenido directamente de Yelp puede violar sus términos de servicio. 
                Te mostramos las mejores prácticas legales para integrar reseñas de Yelp.
            </p>
        </div>
        
        <div class='row'>
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>
                        <h5>Opción 1: Widget Oficial de Yelp</h5>
                    </div>
                    <div class='card-body'>
                        <p>La forma más legal y recomendada de mostrar reseñas de Yelp.</p>
                        <h6>Ventajas:</h6>
                        <ul>
                            <li>✅ Completamente legal</li>
                            <li>✅ Actualizaciones automáticas</li>
                            <li>✅ Diseño oficial de Yelp</li>
                            <li>✅ SEO friendly</li>
                        </ul>
                        <h6>Implementación:</h6>
                        <ol>
                            <li>Ir a <a href='https://biz.yelp.com' target='_blank'>Yelp for Business</a></li>
                            <li>Iniciar sesión con la cuenta del negocio</li>
                            <li>Ir a 'Marketing' → 'Review Widget'</li>
                            <li>Copiar el código del widget</li>
                            <li>Pegar en el sitio web</li>
                        </ol>
                    </div>
                </div>
            </div>
            
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>
                        <h5>Opción 2: Yelp Fusion API</h5>
                    </div>
                    <div class='card-body'>
                        <p>API oficial de Yelp para desarrolladores.</p>
                        <h6>Ventajas:</h6>
                        <ul>
                            <li>✅ Control total del diseño</li>
                            <li>✅ Datos en tiempo real</li>
                            <li>✅ Personalizable</li>
                            <li>✅ Integración con base de datos</li>
                        </ul>
                        <h6>Requisitos:</h6>
                        <ul>
                            <li>Registro en <a href='https://www.yelp.com/developers' target='_blank'>Yelp Developers</a></li>
                            <li>API Key de Yelp</li>
                            <li>Implementación de autenticación</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='row mt-4'>
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>
                        <h5>Opción 3: Reseñas Manuales</h5>
                    </div>
                    <div class='card-body'>
                        <p>Sistema de reseñas propio como respaldo.</p>
                        <h6>Ventajas:</h6>
                        <ul>
                            <li>✅ Control total</li>
                            <li>✅ Sin dependencias externas</li>
                            <li>✅ Personalizable</li>
                            <li>✅ SEO optimizado</li>
                        </ul>
                        <h6>Implementación:</h6>
                        <p>Ya tenemos un sistema de reseñas implementado en el sitio.</p>
                    </div>
                </div>
            </div>
            
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header'>
                        <h5>Opción 4: Screenshots/Imágenes</h5>
                    </div>
                    <div class='card-body'>
                        <p>Capturas de pantalla de las reseñas (con permisos).</p>
                        <h6>Consideraciones:</h6>
                        <ul>
                            <li>⚠️ Requiere permisos de Yelp</li>
                            <li>⚠️ Actualización manual</li>
                            <li>⚠️ Puede violar términos de uso</li>
                            <li>✅ Fácil implementación</li>
                        </ul>
                        <h6>Recomendación:</h6>
                        <p>No recomendado sin autorización explícita de Yelp.</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class='card mt-4'>
            <div class='card-header'>
                <h5>Implementación Recomendada</h5>
            </div>
            <div class='card-body'>
                <h6>Paso 1: Widget Oficial de Yelp</h6>
                <p>Implementar el widget oficial en la página de reseñas.</p>
                
                <h6>Paso 2: Sistema de Reseñas Propio</h6>
                <p>Mantener el sistema actual como respaldo y para reseñas verificadas.</p>
                
                <h6>Paso 3: Enlaces a Yelp</h6>
                <p>Agregar enlaces directos a la página de Yelp del restaurante.</p>
                
                <div class='mt-3'>
                    <a href='https://www.yelp.com/biz/horchata-mexican-food-culver-city' target='_blank' class='btn btn-primary me-2'>
                        <i class='fab fa-yelp me-1'></i>Ver en Yelp
                    </a>
                    <a href='reviews.php' class='btn btn-outline-primary'>
                        <i class='fas fa-star me-1'></i>Reseñas del Sitio
                    </a>
                </div>
            </div>
        </div>
        
        <div class='alert alert-warning mt-4'>
            <h6>⚠️ Advertencia Legal</h6>
            <p>
                Siempre consulta con un abogado antes de implementar cualquier método de extracción 
                de contenido de sitios web de terceros. Yelp tiene políticas estrictas sobre el uso 
                de su contenido.
            </p>
        </div>
    </div>
    
    <script src='https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js'></script>
</body>
</html>";
?>
