<?php declare(strict_types = 1);

namespace Pd\Monitoring\User;

final class Authenticator implements \Nette\Security\Authenticator, \Nette\Security\IdentityHandler
{

	private UsersRepository $usersRepository;

	private \Nette\Security\Passwords $passwords;


	public function __construct(
		UsersRepository $usersRepository,
		\Nette\Security\Passwords $passwords
	)
	{
		$this->usersRepository = $usersRepository;
		$this->passwords = $passwords;
	}


	public function authenticate(string $user, string $password): \Nette\Security\IIdentity
	{
		$user = $this->usersRepository->getBy(['email' => $user]);

		if ($user === NULL) {
			throw new \Nette\Security\AuthenticationException('Uživatelský účet nebyl nalezen');
		}

		if ( ! $this->passwords->verify($password, $user->password)) {
			throw new \Nette\Security\AuthenticationException('Nebylo zadáno správné heslo');
		}

		if ($this->passwords->needsRehash($user->password)) {
			$user->password = $this->passwords->hash($password);
			$user = $this->usersRepository->persist($user);
		}

		if ($user->authtoken === NULL) {
			$user->authtoken = \Nette\Utils\Random::generate(20);
			$user = $this->usersRepository->persist($user);
		}

		$this->usersRepository->flush();

		return $user;
	}


	public function sleepIdentity(\Nette\Security\IIdentity $identity): \Nette\Security\IIdentity
	{
		return new \Nette\Security\SimpleIdentity($identity->authtoken);
	}


	public function wakeupIdentity(\Nette\Security\IIdentity $identity): ?\Nette\Security\IIdentity
	{
		$user = $this->usersRepository->getBy(['authtoken' => $identity->getId()]);

		return $user;
	}

}
