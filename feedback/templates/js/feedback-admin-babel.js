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
		fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=causes&tkn=" + this.props.token)
			.then(response => response.json())
			.then(commits => {
			this.setState({causes: commits, start: true});
		});
	}
	deleteCauses(el) {
		const data = {
			id: el
		};
		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=delete_causes&tkn=' + this.props.token, { data })
			.then(res => {
			this.componentDidMount();
		})
	}
	addCauses() {
		let subject_causes = document.getElementById("subject-causes").value;
		let email_causes = document.getElementById("email-causes").value;
		const data = {
			subject: subject_causes,
			email: email_causes
		};
		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=add_causes&tkn=' + this.props.token, { data })
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
		fetch("/wp-content/plugins/feedback/inquiries.php?inquiries=messages&tkn=" + this.props.token)
			.then(response => response.json())
			.then(commits => {
			this.setState({messages: commits, start: true});
		});
	}
	deleteMessages(el) {
		const data = {
			id: el
		};
		axios.post('/wp-content/plugins/feedback/inquiries.php?inquiries=delete_messages&tkn=' + this.props.token, { data })
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
		this.state = {content: true};
		this.menuItem = this.menuItem.bind(this);
	}
	menuItem(state) {
		this.setState({content: state});
	}
	render() {
		let tkn = document.getElementById("tkn").innerText;
		let element = this.state.content?<Causes token={tkn} />:<Messages token={tkn} />;
		return(
			<div>
				<nav className="menu-navigation-feedback">
					<ul>
						<li onClick={() => this.menuItem(true)}>Причины</li>
						<li onClick={() => this.menuItem(false)}>Сообщения</li>
					</ul>
				</nav>
				<div className="content-feedback">
					{element}
				</div>
			</div>
		);
	}
}

ReactDOM.render(
    <FeedBack />,
    document.getElementById("app")
)