import ReactDOM from 'react-dom';
import App from './components/admin/load/mvx-backend-pages';
document.addEventListener('DOMContentLoaded', function () {
	const element = document.getElementById('mvx-admin-dashboard');
	if (typeof element !== 'undefined' && element !== null) {
		ReactDOM.render(
			<App />,
			document.getElementById('mvx-admin-dashboard')
		);
	}
});
