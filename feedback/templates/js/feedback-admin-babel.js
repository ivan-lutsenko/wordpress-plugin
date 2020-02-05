/**
 * ReactJS file
 *
 * @category Plugin
 * @package  WordPress plugin
 */

/** ReactJS file */
class Causes extends React.Component {
	constructor(props) {
		super(props);
		this.state = {causes: [], start: false};
		this.deleteCauses = this.deleteCauses.bind(this);
		this.addCauses = this.addCauses.bind(this);
	}
	componentDidMount() {
		fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=causes&token=" + this.props.token)
			.then(response => response.json())
			.then(commits => {
			this.setState({causes: commits, start: true});
		});
	}
	deleteCauses(id_cause) {
		let params = new URLSearchParams();
		params.append('id_cause', id_cause);

		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=delete_causes&token=' + this.props.token, params)
			.then(res => {
			this.componentDidMount();
		})
	}
	addCauses() {
		let name_cause = document.getElementById("subject-causes").value;
		let email_cause = document.getElementById("email-causes").value;
		let params = new URLSearchParams();
		params.append('subject', name_cause);
		params.append('email', email_cause);

		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=add_causes&token=' + this.props.token, params)
			.then(res => {
			this.componentDidMount();
		})
	}
	render() {
		return(
			<div className="add-causes">
					<div>
						<input id="subject-causes" style={{margin: "0px"}} type="text" placeholder="Новая причина" />
						<input id="email-causes" type="text" placeholder="Электронная почта для отправки" />
						<button onClick={this.addCauses}>Добавить</button>
					</div>
				<div className="list-causes">
				{
					this.state.start ? (
						this.state.causes.length != 0 ? (
							this.state.causes.map(item =>
								<div className="list-causes-div" key={item.id}>
									<div>Причина: {item.subject}</div>
									<div>Адрес электронной почты: {item.email}</div>
									<button onClick={() => this.deleteCauses(item.id)}>Удалить</button>
								</div>
					)
						) : (
							'Вы еще не добавляли причины'
						)
					) : (
						null
					)
				}
				</div>
			</div>
		);
	}
}

class Messages extends React.Component {
	constructor(props) {
		super(props);
		this.state = {messages: [], start: false};
		this.deleteMessages = this.deleteMessages.bind(this);
	}
	componentDidMount() {
		fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=messages&token=" + this.props.token)
			.then(response => response.json())
			.then(commits => {
			this.setState({messages: commits, start: true});
		});
	}
	deleteMessages(id_message) {
		let params = new URLSearchParams();
		params.append('id_message', id_message);

		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=delete_messages&token=' + this.props.token, params)
			.then(res => {
			this.componentDidMount();
		})
	}
	render() {
		return(
			<div className="list-causes">
			{
				this.state.start ? (
					this.state.messages.length != 0 ? (
						this.state.messages.map(item =>
							<div className="list-causes-div" key={item.id}>
								<div>Имя: {item.name}</div>
								<div>Адрес электронной почты: {item.email}</div>
								<div>Причина: {item.subject}</div>
								<div>Описание причины: {item.description}</div>
								<div>Версия браузера: {item.version}</div>
								<div>Ссылка на скриншот: {item.link}</div>
								<div>
									<img src={"/wp-content/plugins/feedback/" + item.link} alt="Скриншот" style={{width: "100px", height: "auto"}} />
								</div>
								<button onClick={() => this.deleteMessages(item.id)}>Удалить</button>
							</div>
				)
					) : (
						'Еще не оставляли обратную связь'
					)
				) : (
					null
				)
			}
			</div>
		);
	}
}

class FeedBack extends React.Component {
	constructor(props) {
		super(props);
		this.state = {content: true, token: '', start: false};
		this.menuItem = this.menuItem.bind(this);
	}
	componentDidMount() {
		fetch("/wp-content/plugins/feedback/token.php")
			.then(response => response.text())
			.then(commits => this.setState({token: commits, start: true}));
	}
	menuItem(state) {
		this.setState({content: state});
	}
	render() {
		return(
			<div>
				<nav className="menu-navigation-feedback">
					<ul>
						<li onClick={() => this.menuItem(true)}>Причины</li>
						<li onClick={() => this.menuItem(false)}>Сообщения</li>
					</ul>
				</nav>
				<div className="content-feedback">
					{
						this.state.start ? (
							this.state.content ? (
								<Causes token={this.state.token} />
							) : (
								<Messages token={this.state.token} />
							)
						) : (
							null
						)
					}
				</div>
			</div>
		);
	}
}

ReactDOM.render(
    <FeedBack />,
    document.getElementById("app")
)
