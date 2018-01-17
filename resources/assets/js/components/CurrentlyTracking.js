import React, { Component } from 'react';

class CurrentlyTracking extends React.Component {
    constructor(props) {
        super(props);
        this.state = {number: 0, length: 55};

        // setInterval(this.update, 500);
    }

    componentDidMount() {
        this.timerID = setTimeout(()=>this.update(), 1000);
    }

    componentWillUnmount() {
      clearTimeout(this.timerID);
    }

    secondsToTime(secs){;
        let dur = moment.duration(secs, 'seconds');

        let time = (dur.days() ? dur.days() + ' days ' : '') + (dur.hours() ? dur.hours() + ' hours ' : '') +  dur.minutes() + " minutes " + (dur.hours() ? '' : dur.seconds() + "s");

        return time;
    }

    update() {
        var self = this;

        // do an ajax request to the api and get the updated currently tracking
        // Make a request for a user with a given ID
        axios.get('/api/currently-tracking')
          .then(function (response) {
              var task = response.data.data[0];

              self.setState({
               length: task['length'],
               time: self.secondsToTime(task['length']),
               task: task.task,
               notes: task.notes
              });
              self.timerID = setTimeout(()=>self.update(), 1000);

          })
          .catch(function (error) {
            console.log(error);
            self.timerID = setTimeout(()=>self.update(), 2000);

          });


    }

    render() {
        return (
            <div className="currentlyTracking">
                <div className="title m-b-md">
                    Currently Tracking:
                </div>
                <span className="title">
                    <strong>{this.state.task}</strong>
                </span>

                <strong> {this.state.time}</strong>
                <br />
                <em>{this.state.notes}</em>
            </div>
        )
    }
}

export default CurrentlyTracking;