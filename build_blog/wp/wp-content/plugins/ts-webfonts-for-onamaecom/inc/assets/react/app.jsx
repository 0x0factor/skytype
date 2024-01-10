// Load Library
import React from 'react'
import {
	render
} from 'react-dom'
import './functions/events.jsx'


const Thead = React.createClass({
	render() {
		return (
			<thead>
				<tr><th>　適用箇所</th><th>　書体分類</th><th>　フォントファミリー名</th><th>　フォント名</th></tr>
			</thead>
		)
	}
})

const SelectFont = React.createClass({
	getInitialState() {
		return {
			checked: ['unselect']
		}
	},
	handleChange( event ) {
		var checked = [];
		var sel = event.target;
		for ( var i = 0; i < sel.length; i++ ) {
			var option = sel.options[i];
			if ( option.selected ) {
				checked.push( option.value );
			}
		}
		this.setState({
			checked: checked
		});
	},
	getOptions() {
		var i = 0;
		var selectedValue = 'false';
		var optionNodes = [];
		var selected = current_font[this.props.type];

		if ( ! this.props.selectedFamily[0] ) {
			if ( ! selected || this.props.selectedType[0] ) {
				optionNodes.push(<option key={i} value='false'>書体分類・フォントファミリー名を選択してください</option>);
				var options = {
					selected: selectedValue,
					nodes: optionNodes
				};
				return options;
			}
		}

		if ( selected ) {
			var key1 = selected.type;
			var key2 = selected.family;
			var selectedValue = selected.font;
		}

		if ( 'unselect' !== this.state.checked[0] ) {
			var selectedValue = this.state.checked[0];
		}

		if ( this.props.selectedType[0] || this.props.selectedFamily[0] ) {
			if ( this.props.selectedType[0] ) {
				var key1 = this.props.selectedType[0];
			}
			if ( this.props.selectedFamily[0] ) {
				var key2 = this.props.selectedFamily[0];
			}
		}

		var dataCount = Object.keys(this.props.data).length;
		if ( 0 !== dataCount ) {
			console.log();
			if ( ! this.props.data[key1] || ! this.props.data[key1][key2]) {
				i++;
				optionNodes.push(<option key={i} value='false'>書体分類・フォントファミリー名を選択してください</option>);
			} else {
				var font = this.props.data[key1][key2];
				optionNodes.push(<option key={i} value='false'>設定しない</option>);
				Object.keys( font ).forEach( function( key ) {
					i++;
					var value = this[key];
					optionNodes.push(<option key={i} value={value}>{value}</option>);
				}, font );
			}
		}

		var options = {
			selected: selectedValue,
			nodes: optionNodes
		};
		return options;
	},
	render() {
		var name = 'typesquare_custom_theme[fonts][' + this.props.type + '][font]';
		var options = this.getOptions();
		var selectedValue = options.selected;
		var optionNodes = options.nodes;
		return (
			<select name={name} onChange={this.handleChange} value={selectedValue}>{ optionNodes }</select>
		)

	}
})

const SelectFamily = React.createClass({
	getInitialState() {
		return {
			checked: ['unselect']
		}
	},
	handleChange( event ) {
		var checked = [];
		var sel = event.target;
		for ( var i = 0; i < sel.length; i++ ) {
			var option = sel.options[i];
			if ( option.selected ) {
				checked.push( option.value );
			}
		}
		this.props.onChangeFamily( checked );
		this.setState({
			checked: checked
		});
	},
	getOptions() {
		var optionNodes = [];
		var i = 0;
		var selectedValue = 'false';
		var selected = current_font[this.props.type];
		if ( ! this.props.selected && ! selected ) {
			optionNodes.push(<option key={i} value='false'>設定なし</option>);
			var options = {
				selected: selectedValue,
				nodes: optionNodes
			};
			return options;
		}


		if ( this.props.selected ) {
			var key = this.props.selected[0];
		} else {
			var key = selected.type;
		}

		if ( 'unselect' !== this.state.checked[0] ) {
			var selectedValue = this.state.checked[0];
		} else if ( selected ) {
			var selectedValue = selected.family;
		}

		var dataCount = Object.keys(this.props.data).length;
		if ( 0 === dataCount ) {
			optionNodes.push(<option key={i} value='false'>Loading...</option>);
		} else {
			var family = this.props.data[key];
			if ( ! family ) {
				optionNodes.push(<option key={i} value='false'>設定なし</option>);
			} else {
				optionNodes.push(<option key={i} value='false'>フォントファミリー名を選択してください</option>);
				if ( ! family[selectedValue] ) {
					var selectedValue = 'false';
				}
				Object.keys( family ).forEach( function( key ) {
					i++;
					var value = this[key];
					optionNodes.push(<option key={i} value={key}>{key}</option>);
				}, family );
			}
		}

		var options = {
			selected: selectedValue,
			nodes: optionNodes
		};
		return options;
	},
	render() {
		var name = 'typesquare_custom_theme[fonts][' + this.props.type + '][family]';
		var options = this.getOptions();
		var selectedValue = options.selected;
		var optionNodes = options.nodes;
		return (
			<select name={name} onChange={this.handleChange} value={selectedValue}>{ optionNodes }</select>
		)

	}
})

const SelectType = React.createClass({
	getInitialState() {
		return {
			checked: ['unselect']
		}
	},
	handleChange( event ) {
		var checked = [];
		var sel = event.target;
		for ( var i = 0; i < sel.length; i++ ) {
			var option = sel.options[i];
			if ( option.selected ) {
				checked.push( option.value );
			}
		}
		this.props.onChangeType( checked );
		this.setState({
			checked: checked
		});
	},
	render() {
		var optionNodes = [];
		var i = 0;
		var name = 'typesquare_custom_theme[fonts][' + this.props.type + '][type]';
		var selected = current_font[this.props.type];
		var selectedValue = 'false';
		if ( selected ) {
			if ( 'unselect' == this.state.checked[0] ) {
				selectedValue = selected.type;
			}
		}
		if ( 'unselect' != this.state.checked[0] ) {
			selectedValue = this.state.checked[0];
		}

		optionNodes.push(<option key={i} value='false'>設定なし</option>);
		Object.keys( this.props.data ).forEach( function( key ) {
			i++;
			var value = this[key];
			optionNodes.push(<option key={i} value={key}>{key}</option>);
		}, this.props.data );
		return (
			<select name={name} onChange={this.handleChange} value={selectedValue}>{ optionNodes }</select>
		)

	}
})

const Row = React.createClass({
	getInitialState() {
		return {
			type: '',
			family: '',
			name: ''
		}
	},
	changeType( selected ) {
		this.setState({
			type: selected
		})
	},
	changeFamily( selected ) {
		this.setState({
			family: selected
		})
	},
	getTotalRow() {
		return (
			<tr>
				<th>　{this.props.title}</th>
				<td>
					<SelectType
						onChangeType={this.changeType}
						data={this.props.data}
						type={this.props.type}/>
				</td>
				<td>
					<SelectFamily
						onChangeFamily={this.changeFamily}
						data={this.props.data}
						selected={this.state.type}
						type={this.props.type}/>
				</td>
				<td>
					<SelectFont
						data={this.props.data}
						selectedType={this.state.type}
						selectedFamily={this.state.family}
						selectedName={this.state.name}
						type={this.props.type}/>
				</td>
			</tr>
		)
	},
	getPostRow() {
		return (
			<tr>
				<th>　{this.props.title}</th>
				<td>
					<p>
						<label>書体分類：</label>
						<SelectType
							onChangeType={this.changeType}
							data={this.props.data}
							type={this.props.type}/>
					</p>
					<p>
						<label>フォントファミリー名：</label>
						<SelectFamily
							onChangeFamily={this.changeFamily}
							data={this.props.data}
							selected={this.state.type}
							type={this.props.type}/>
					</p>
					<p>
						<label>フォント名：</label>
						<SelectFont
							data={this.props.data}
							selectedType={this.state.type}
							selectedFamily={this.state.family}
							selectedName={this.state.name}
							type={this.props.type}/>
					</p>
				</td>
			</tr>
		)
	},
	render() {
		if ( 'total' == this.props.pageType) {
			var rowNode = this.getTotalRow();
		} else if ( 'post' == this.props.pageType ) {
			var rowNode = this.getPostRow();
		} else {
			var rowNode = <tr><td>NoData</td></tr>;
		}
		return rowNode
	}

})

const Tbody = React.createClass({
	loadPostsFromServer() {
		jQuery.ajax({
			type: "GET",
			url: json_endpoint,
			dataType: 'json',
			cache: false,
			success: function(data) {
				this.setState({data: data});
			}.bind(this),
			error: function(xhr, status, err) {
				console.error(this.props.url, status, err.toString());
			}.bind(this)
		});
	},
	getInitialState() {
		return {
			data: []
		};
	},
	componentDidMount() {
		this.loadPostsFromServer();
	},
	render() {
		return (
			<tbody>
				<Row title='タイトル' type='title' data={this.state.data} pageType={this.props.pageType} />
				<Row title='リード' type='lead' data={this.state.data} pageType={this.props.pageType} />
				<Row title='本文' type='text' data={this.state.data} pageType={this.props.pageType} />
				<Row title='太字' type='bold' data={this.state.data} pageType={this.props.pageType} />
			</tbody>
		)
	}
})

const App = React.createClass({
	render() {
		if ( 'total' == this.props.pageType) {
			var headNode = <Thead />;
		}
		return(
			<table className='widefat form-table'>
				{headNode}
				<Tbody pageType={this.props.pageType}/>
			</table>
		)
	}
});


if ( document.getElementById("ts-react-search-font") != null ) {
	render( ( <App pageType='total'/> ), document.getElementById('ts-react-search-font'));
} else if ( document.getElementById("ts-react-post-search-font") != null ) {
	render( ( <App pageType='post'/> ), document.getElementById('ts-react-post-search-font'));
}
