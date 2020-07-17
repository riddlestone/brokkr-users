# Brokkr-Users Controllers and Actions

## Account Controller

This controller handles a single user's account, determined by the current authenticated user.

### Index Action

This provides details of the current authenticated user.

| Property    | Value                                                   |
| ----------- | ------------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\AccountController` |
| Action      | `index`                                                 |
| View Script | `brokkr/users/account/index`                            |

| View Variable | Description            | Type                                   |
| ------------- | ---------------------- | -------------------------------------- |
| `$user`       | The authenticated user | `Riddlestone\Brokkr\Users\Entity\User` |

### Login Action

This provides a login form, and authenticates users if the POSTed details are correct.
A successful authentication results in a redirect to the home page.

| Property    | Value                                                   |
| ----------- | ------------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\AccountController` |
| Action      | `login`                                                 |
| View Script | `brokkr/users/account/login`                            |

| View Variable | Description    | Type                                      |
| ------------- | -------------- | ----------------------------------------- |
| `$form`       | The login form | `Riddlestone\Brokkr\Users\Form\LoginForm` |

### Logout Action

This clears the current authentication, and redirects to the home page.

| Property    | Value                                                   |
| ----------- | ------------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\AccountController` |
| Action      | `logout`                                                |
| View Script | N/A                                                     |

### Request Password Reset Link Action

This provides a password reset request form, and sends an reset link to the given email address, if it is for a valid
user. A successful submission results in a redirect to the home page.

Note that the success and redirect occur regardless of whether the user is valid, and therefore a reset link is sent, to
prevent this feature from being used to identify site users.

| Property    | Value                                                   |
| ----------- | ------------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\AccountController` |
| Action      | `request-password-reset-link`                           |
| View Script | `brokkr/users/account/request_password_reset_link`      |

| View Variable | Description            | Type                                                     |
| ------------- | ---------------------- | -------------------------------------------------------- |
| `$form`       | The reset request form | `Riddlestone\Brokkr\Users\Form\RequestPasswordResetForm` |

### Reset Password Action

This provides a password reset form, and resets the password for the user. A successful submission results in a redirect
to the home page, with the user logged in.

| Property    | Value                                                   |
| ----------- | ------------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\AccountController` |
| Action      | `reset-password`                                        |
| View Script | `brokkr/users/account/reset_password`                   |

| View Variable | Description             | Type                                              |
| ------------- | ----------------------- | ------------------------------------------------- |
| `$form`       | The password reset form | `Riddlestone\Brokkr\Users\Form\PasswordResetForm` |

## Users Controller

This provides controls for administrators to manage users.

### Index Action

This provides a list of users.

| Property    | Value                                                 |
| ----------- | ----------------------------------------------------- |
| Controller  | `Riddlestone\Brokkr\Users\Controller\UsersController` |
| Action      | `index`                                               |
| View Script | `brokkr/users/users/index`                            |

| Request Parameter | Location | Type       | Description                                 |
| ----------------- | -------- | ---------- | ------------------------------------------- |
| `page`            | Query    | `int/null` | Which page of users to show, defaults to 1 |

| View Variable | Description             | Type                                     |
| ------------- | ----------------------- | ---------------------------------------- |
| `$users`      | The users to list       | `Riddlestone\Brokkr\Users\Entity\User[]` |
| `$page`       | The current page number | `int`                                    |
| `$pages`      | The total page count    | `int`                                    |
