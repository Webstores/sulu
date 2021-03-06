// @flow
import React from 'react';
import {action, computed, observable} from 'mobx';
import {observer} from 'mobx-react';
import {ResourceMultiSelect} from 'sulu-admin-bundle/containers';
import {Loader, Grid} from 'sulu-admin-bundle/components';
import {localizationStore} from 'sulu-admin-bundle/stores';
import type {Localization} from 'sulu-admin-bundle/stores';
import RoleAssignment from './RoleAssignment';
import roleAssignmentsStyle from './roleAssignments.scss';

type Props = {|
    disabled: boolean,
    onChange: (value: Array<Object>) => void,
    value: Array<Object>,
|};

@observer
class RoleAssignments extends React.Component<Props> {
    static defaultProps = {
        disabled: false,
    };

    @observable localizations: ?Array<Localization>;

    componentDidMount() {
        localizationStore.loadLocalizations()
            .then(action((localizations) => {
                this.localizations = localizations;
            }));
    }

    @computed get selectedRoles(): Array<number> {
        const selectedRoles = [];
        for (const currentUserRole of this.props.value) {
            selectedRoles.push(currentUserRole.role.id);
        }

        return selectedRoles.sort();
    }

    handleRoleChange = (newRoleIds: Array<number>, newRoles?: Array<Object> = []) => {
        const newValue = [];

        for (const currentUserRole of this.props.value) {
            if (!newRoleIds.includes(currentUserRole.role.id)) {
                continue;
            }

            newValue.push(currentUserRole);
        }

        const rolesToAdd = newRoles.filter((newSelectedRole) => {
            return !this.selectedRoles.includes(newSelectedRole.id);
        });
        for (const role of rolesToAdd) {
            newValue.push({
                locales: [],
                role: role,
            });
        }

        this.props.onChange(newValue);
    };

    handleRoleAssignmentChange = (newRoleAssignment: Object) => {
        const newValue = [];

        for (const currentUserRole of this.props.value) {
            if (currentUserRole.role.id === newRoleAssignment.role.id) {
                newValue.push(newRoleAssignment);

                continue;
            }

            newValue.push(currentUserRole);
        }

        this.props.onChange(newValue);
    };

    render() {
        const {disabled, value} = this.props;
        const localizations = this.localizations;

        if (!localizations) {
            return <Loader />;
        }

        return (
            <Grid>
                <Grid.Item colSpan={6}>
                    <ResourceMultiSelect
                        apiOptions={{sortBy: 'name'}}
                        disabled={disabled}
                        displayProperty="name"
                        onChange={this.handleRoleChange}
                        resourceKey="roles"
                        values={this.selectedRoles}
                    />
                </Grid.Item>
                {this.selectedRoles.length > 0 &&
                    <Grid.Item colSpan={12}>
                        <table className={roleAssignmentsStyle.roleAssignments}>
                            <tbody>
                                {value.map((userRole, key) => {
                                    return (
                                        <RoleAssignment
                                            disabled={disabled}
                                            key={key}
                                            localizations={localizations}
                                            onChange={this.handleRoleAssignmentChange}
                                            value={userRole}
                                        />
                                    );
                                })}
                            </tbody>
                        </table>
                    </Grid.Item>
                }
            </Grid>
        );
    }
}

export default RoleAssignments;
