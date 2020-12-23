Symfony 4 Rest API Using FOSBundle with JWT
===========================================

Rest API for Meetings

$ cp .env.dist .env

$ composer install

$ php -S localhost:9100 -t public


![Api Flow](http://learningpage.in/uploads/meeting_api.png) 

Please download Postman file to Explore API Endpoints 

# RabbitMQ Installation
https://www.linuxhelp.com/how-to-install-rabbitmq-on-linuxmint-18-3/

* @OA\Parameter(
     *     name="order",
     *     in="query",
     *     description="The field used to order meetings",
     *     @OA\Schema(type="string")
     * )
     * @OA\Tag(name="Meetings")
     * @Security(name="Bearer")
     *
     *
     *  @OA\Parameter(
 *     name="body",
 *     in="path",
 *     required=true,
 *     @OA\JsonContent(
 *        type="object",
 *        @OA\Property(property="property1", type="number"),
 *        @OA\Property(property="property2", type="number"),
 *     ),
 * )
 *
 * @OA\Response(
 *     response=200,
 *     description="",
 *     @OA\JsonContent(
 *        type="object",
 *        @OA\Property(property="property1", type="number"),
 *        @OA\Property(property="property2", type="number"),
 *     )
 * )
 *
 *  *     @OA\RequestBody(
     *         description="Updated user object",
     *         required=true,
     *       @OA\MediaType(
     *           mediaType="multipart/form-data",
     *           @OA\Schema(ref=@Model(type=User::class))
     *        )
     *     )

