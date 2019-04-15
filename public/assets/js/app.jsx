require('../css/app.css');

let React = require('react');
let ReactDOM = require('react-dom');
let axios = require('axios');

const MAX_LENGTH = 20;
const CALC_URL = '/calculate';

function Display(props) {
    return (
        <input type="text" id="display" readOnly value={props.expression}/>
    );
}

class Button extends React.Component {
    constructor(props) {
        super(props);
        this.state = {expression: ''};
    }

    render() {
        return (
            <span className={this.props.class}
                  onClick={() => this.handleClick(this.props.value)}>{this.props.value}</span>
        );
    }
}

class ButtonType extends Button {
    handleClick(symbol) {
        this.props.input(symbol);
    }
}

class ButtonClear extends Button {
    handleClick() {
        this.props.clear();
    }
}

class ButtonCorrect extends Button {
    handleClick() {
        this.props.correct();
    }
}

class ButtonExecute extends Button {
    handleClick() {
        this.props.execute();
    }
}

class Form extends React.Component {
    constructor(props) {
        super(props);
        this.input = this.input.bind(this);
        this.correct = this.correct.bind(this);
        this.clear = this.clear.bind(this);
        this.execute = this.execute.bind(this);
        this.state = {expression: ''};
    }

    input(symbol) {
        let current = this.state.expression;

        if (/[a-zA-Z]/.test(current)) {
            current = '';
        }

        const expression = current + '' + symbol;

        if (expression.length < MAX_LENGTH) {
            this.setState({expression: expression});
        }
    }

    correct() {
        const expression = this.state.expression;
        this.setState({expression: expression.slice(0, -1)});
    }

    clear() {
        this.setState({expression: ''});
    }

    execute() {
        if ('' === this.state.expression) {
            return false;
        }

        let context = this;

        axios.post(CALC_URL, {
            method: 'POST',
            data: this.state.expression,
        })
            .then(function (response) {
                console.log(response.data.result);
                context.setState({expression: response.data.result});
            })
            .catch(function (error) {
                context.setState({expression: error});
            });
    }

    render() {
        const expression = this.state.expression;

        return (
            <form name="calc" id="calculator">
                <table>
                    <tbody>
                    <tr>
                        <td>
                            <label>
                                <Display expression={expression}/>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td className="buttons">
                            <ButtonType name={'open'} value={'('} class="btn operator" input={this.input}/>
                            <ButtonType name={'close'} value={')'} class="btn operator" input={this.input}/>
                            <ButtonCorrect name={'correct'} value={'<='} class="btn operator"
                                           correct={this.correct}/>
                            <ButtonClear name={'clear'} value={'AC'} class="btn operator" clear={this.clear}/>

                            <ButtonType name={'seven'} value={'7'} class="btn" input={this.input}/>
                            <ButtonType name={'eight'} value={'8'} class="btn" input={this.input}/>
                            <ButtonType name={'nine'} value={'9'} class="btn" input={this.input}/>
                            <ButtonType name={'mul'} value={'*'} class="btn operator" input={this.input}/>

                            <ButtonType name={'four'} value={'4'} class="btn" input={this.input}/>
                            <ButtonType name={'five'} value={'5'} class="btn" input={this.input}/>
                            <ButtonType name={'six'} value={'6'} class="btn" input={this.input}/>
                            <ButtonType name={'div'} value={'/'} class="btn operator" input={this.input}/>

                            <ButtonType name={'one'} value={'1'} class="btn" input={this.input}/>
                            <ButtonType name={'two'} value={'2'} class="btn" input={this.input}/>
                            <ButtonType name={'three'} value={'3'} class="btn" input={this.input}/>
                            <ButtonType name={'add'} value={'+'} class="btn operator" input={this.input}/>

                            <ButtonType name={'zero'} value={'0'} class="btn" input={this.input}/>
                            <ButtonType name={'dot'} value={'.'} class="btn" input={this.input}/>
                            <ButtonExecute name={'done'} value={'='} class="btn operator exec" execute={this.execute}/>
                            <ButtonType name={'sub'} value={'-'} class="btn operator" input={this.input}/>
                        </td>
                    </tr>
                    </tbody>
                </table>
            </form>
        );
    }
}

ReactDOM.render(<Form/>, document.getElementById('root'));
