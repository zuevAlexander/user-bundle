user-bundle
===================

Security component for User authentication

## Installing

1. Add a new repository to the composer.json of your project:

"repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/zuevAlexander/user-bundle.git"
        }
    ]
	
2. Add a new package to requirements:
    "require": {
        //...other packages...//
        "nd-symfony/user-bundle": "dev-master"
    },

3. Run "composer update" command.

4. Enable the bundle by adding the following line in the app/AppKernel.php file of your project:

// ...
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            // ...other bundles...//
            new NorseDigital\UserBundle\UserBundle(),
        );
        // ...
    }
    // ...
}

5. Import services of the bundle by adding the following line in the app/config.yml file of your project:
imports:
    //...other services...//
    - { resource: "@UserBundle/Resources/config/services.yml" }


6. Create an object for users. This class must implement the following interfaces:

    NorseDigital\UserBundle\User\UserInterface
    
 
7. Create Repository for this class. This class must extend one of the following classes:
    NorseDigital\Symfony\RestBundle\Repository\EntityRepository
    Doctrine\ORM\EntityRepository
    
    
8. Add these parameters to app/config.yml and configure them with your project:
parameters:
     //...other parameters...//
    authenticate_token_name: your-token-name
    user_bundle.entity.user: CoreBundle\Entity\User
    user_bundle.repository.user.class: CoreBundle\Repository\UserRepository


9. Configure security. Replace the contents of the security.yml file of your project with the following code:
security:
    encoders:
        CoreBundle\Entity\User:
            algorithm: bcrypt
            cost: 12

    providers:
        webservice:
            id: user_bundle.webservice_user_provider

    firewalls:
        dev:
            pattern: ^/_wdt
            security: false

        api_doc:
            pattern: ^/doc
            security: false

        register:
            pattern: ^/register
            security: false

        login:
            pattern: ^/login
            stateless: true
            guard:
                authenticators:
                    - user_bundle.password_authenticator

        main:
            pattern: ^/
            stateless: true
            guard:
                authenticators:
                    - user_bundle.token_authenticator

#    role_hierarchy:
#        ROLE_ADMIN:       ROLE_USER
#        ROLE_SUPER_ADMIN: [ROLE_ADMIN, ROLE_USER]
#
#    access_control:
#        - { path: ^/admin, roles: ROLE_ADMIN }


10. Inject encoder which implement UserPasswordEncoderInterface to your UserService and use one for the password when adding a new user to BD. For example:

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(
        UserPasswordEncoderInterface $encoder
    ) {
        $this->encoder = $encoder;
    }

    /**
     * @param UserRegisterRequest $request
     *
     * @return User
     */
    public function createUser(UserRegisterRequest $request): User
    {
        $user = $this->createEntity();
        $user->setUsername($request->getUsername());
        $user->setPassword(
            $this->encoder->encodePassword($user, $request->getPassword())  /* pay attention to this line */ 
        );
        $user->setRoles(array('ROLE_USER'));
        $this->generateApiKey($user);

        return $user;
    }
}

In configuration:
  
  core.service.user:
    class: '%core.service.user.class%'
    arguments:
      - "@security.password_encoder"
            
            
