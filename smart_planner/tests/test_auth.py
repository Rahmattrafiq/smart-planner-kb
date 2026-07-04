import unittest

from app import app, database


class AuthFlowTestCase(unittest.TestCase):
    def setUp(self):
        app.config.update(TESTING=True)
        self.client = app.test_client()
        self.client.get('/logout')

    def test_login_page_is_available(self):
        response = self.client.get('/login')
        self.assertEqual(response.status_code, 200)
        self.assertIn(b'Login', response.data)

    def test_registration_rejects_duplicate_email(self):
        database.register_user('Test User', 'test@example.com', 'secret123')
        response = self.client.post('/register', data={
            'nama': 'Another User',
            'email': 'test@example.com',
            'password': 'secret123',
            'confirm_password': 'secret123',
        }, follow_redirects=True)
        self.assertIn(b'Email sudah terdaftar', response.data)


if __name__ == '__main__':
    unittest.main()
