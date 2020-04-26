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
