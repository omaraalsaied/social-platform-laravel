
# Laravel Social Platform

this is a social platform APIs Where you can do basic authentication, posting, commenting, managing friends & sharing posts on your profile.

## Installation

clone this project using command line

```bash
  git clone https://github.com/omaraalsaied/social-platform-laravel.git
  
  cd social-platform-laravel

  composer install 

  php artisan key:generate

```

open your .env file and place the right configuration for Database connection

```bash
php artisan migrate
php artisan db:seed
```

and to run your application 

```bash
php artisan ser
```


for testing the APIs you'll need Postman

for better results while using the endpoints in Postman make sure to add a header with the key "Accept" and value of "applicatoin/json" 


## API Reference

### Auth Routes
#### Registering new users

```http
  POST /api/v1/auth/register
```

| Parameter | Type     | Description                |
| :-------- | :------- | :------------------------- |
| `name` | `string` | **Required**. |
| `email` | ` email`   | **Required**.  |
| `password` | `string` | **Required**.  |
| `phone` | `string` | **Required**.  |

#### User Login
returns token that the user will use in all of authenticated routes.

```http
  POST /api/v1/auth/login
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `email` | **Required**.  |
| `paswword`      | `string` | **Required**.  |


### Social Login 
#### only github is supported now
social login is implemented using: https://github.com/laravel/socialite

the package makes it easy to implement login\register with social platforms, for sake of saving time i just made it with github.

```http
  GET /api/v1/auth/login/{provider}
```
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `provider`      | `string` | **Required**.  |

the package redirects the user to the Signin page of the provider (platform he wishes to sign in\up with) and after the user auhorizes his data access the application redirects to callback endpoint where it's checked if the user exists so he should be logged in or he's a new user so he should be registered.

#### note that both endpoints of social login should be used with web browsers & not Postman
```http
    GET /api/v1/auth/login/{provider}
```


#### password resetting
this route is for the server to handle sending a reset password email to the user.

##### for quicker testing of this endpoint, in .env file make the ```MAIL_MAILER=log``` and you'll find the password reset mail in your application's logs


```http
  POST /api/v1/auth/reset-passowrd-submit
```

| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `email` | **Required**.  |

in the email there's a link to a local view that asks the user to input new password and submits it to this endpoint to validate and update the user
```http
 POST /api/v1/auth/confirm-reset
```
| Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `email`      | `email` | **Required**.  |
| `token`      | `string` | **Required**.  |
| `password`      | `string` | **Required**.  |

token is created by the server to authenticate the process and prevent data forgery.


#### Logout
```http
 POST /api/v1/auth/logout
```
this route demands the user to be authenticated. don't forget to add the token in the Postman Headers.


### User endpoints

```http 
    GET /api/v1/users/{id}
```
this endpoint is used to view any user's profile
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `id`      | `string` | **Required**.  |

it shows the user's profile with posts he wrote and shared posts.


```http 
    POST /api/v1/users/search
```
this endpoint is used to view any user's profile
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `search_param`      | `string` | **Required**.  |

this endpoint is used to search users by querying the database for any near matches of the search_param against name,phone & bio.

```http 
    POST /api/v1/users/update
```
this endpoint is used to view any user's profile
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `name`        | `string`      | **string,max:255**.  |
| `email`       | `string`      | **string,email,max:255**.  |
| `paswword`    | `string`      | **string,min:8**.  |
| `phone`       | `string`      | **string,min:11,max:13**.  |
| `profile_pic` | `string`      | **string,max:255**.  |
| `bio`         | `string`      | **string,max:255**.  |

this endpoint is used to update the user's data.


```http 
    GET /api/v1/user/profile
```
The endpoint is used to make the user view his own profile with his data and posts


### Posts endpoint

#### Feed
```http 
    GET /api/v1/posts
```
this is index of posts simillar to a news feed endpoint where posts appear with likes & comments count.

#### show a post
```http 
    GET /api/v1/posts/{id}
```
views the post with the id in the url with the posts comments and likes on the post and each comment


#### add a post
```http 
    POST /api/v1/posts/
```
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `title`      | `string` | **Required**.  |
| `body`        | `string` | **Required**.  |


#### update post
user can only update posts of his own
```http 
    POST /api/v1/posts/update{id}
```
 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `title`      | `string` | **Required**.  |
| `body`        | `string` | **Required**.  |

#### delete post
user can only delete posts of his own

```http 
    DELETE /api/v1/posts/destroy/{id}
```

#### Like or Unlike a post
Users can Interact with posts. The interactions between users and posts and users among eachother like sending friend requests is implemented by laravel-acquaintances package :  https://github.com/multicaret/laravel-acquaintances

```http 
    POST /api/v1/posts/interact/{id}
```

 Parameter | Type     | Description                       |
| :-------- | :------- | :-------------------------------- |
| `like`      | `boolean` | **Required**.  |

user can like a post and unlike it if he did like it.
user can't like a post twice or unlike it twice.

#### Who liked this Post?

to view users who like this post

```http 
    GET /api/v1/posts/likers/{id}
```

#### Sharing a post to your profile

```http 
    POST /api/v1/posts/share/{id}
```
User can share other users posts on his profile or even his own posts.


### Comments on Posts

#### Add a comment on Post
```http 
    POST /api/v1/comments/store/{post_id}
```
 Parameter | Type     | Description                       |
| :--------    | :------- | :-------------------------------- |
| `body`      | `string` | **Required**.  |

#### view a comment on Post
```http 
    GET /api/v1/comments/{id}
```
 Parameter | Type     | Description                       |
| :--------    | :------- | :-------------------------------- |
| `body`      | `string` | **Required**.  |


#### update a comment on Post

```http 
    PATCH /api/v1/comments/{id}
```
 Parameter | Type     | Description                       |
| :--------    | :------- | :-------------------------------- |
| `body`      | `string` | **Required**.  |


#### delete a comment on Post

```http 
    DELETE /api/v1/comments/destroy/{id}
```

#### liking a Comment
```http 
    POST /api/v1/comments/interact/{id}
```

#### Who liked this Comment

```http 
    GET /api/v1/comments/likers/{id}
```

### Friendships Between Users
users can send friend requests to other ones. the reciepients can accepts or decline the friend requests.

#### Sending A friend request

```http 
    POST /api/v1/friendships/send-request/{id}
```
the authenticated user can send friend requests to other users with the {id} in the url.

#### View pending friend request

```http 
    POST /api/v1/friendships/send-request/{id}
```
the authenticated user can send friend requests to other users with the {id} in the url.

#### respond to a pending friend request

```http 
    POST /api/v1/friendships/respond-to-request/{id}
```
the authenticated user can accept or decline the friend request sent from the user with the {id} mentioned in the url


#### respond to a pending friend request

```http 
    GET /api/v1/friendships/list
```
returns all friends that the user has 

### Managing a user/friend

```http 
    POST /api/v1/friendships/manage/{id}
```

user can manage any other user "he can unfriend them if they're friends, block them or unblock them" the action is taken upon the user with the {id} in url

 Parameter      | Type     | Description                       |
| :--------     | :-------  | :-------------------------------- |
| `action`      | `[unfriend,block,unblock]` | **Required**.  |


