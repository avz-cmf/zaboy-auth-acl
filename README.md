# zaboy-auth-acl 
# Модуль для авторизации
    - Middleware
        - `IdentificationMiddleware` идентифицирует пользователя и записывает в атрибут запроса его роль. 
            В случае если параметры авторизации заданы в заголовках, авторизирует пользователя.
        - `AuthorizationMiddleware` исполняет роль псевдо ACL 
        - `AuthErrorHandlerMiddleware` обрабатывеает ошибку "You are not authorized" - редиректит пользователей на страницу логина 
    - Adapter
        - `AuthAdapter`  - адапрер для аутентификации пользователей 
    - Action
        - `LogoutAction` - действие для выхода из системы