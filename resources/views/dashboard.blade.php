@section('style')
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.2/dist/sweetalert2.min.css" rel="stylesheet"
          type="text/css"/>
@endsection

@section('script')
    @include('sweetalert::alert')
    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@9.17.2/dist/sweetalert2.min.js"
            type="text/javascript"></script>
    <script>
        function deleted(url, id) {
            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: 'btn btn-success',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            })

            swalWithBootstrapButtons.fire({
                title: 'Are you sure?',
                text: "You won't be delete this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes',
                cancelButtonText: 'No',
                reverseButtons: false,
                allowOutsideClick: false
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(url)
                        .then(function (response) {
                            if (response.data.status == 200) {
                                $('#' + id).remove();
                            } else {
                                Swal.fire(
                                    'Good job!',
                                    response.data.message,
                                    'error'
                                )
                            }
                        })
                        .catch(function (error) {
                            Swal.fire(
                                'Good job!',
                                'Something went wrong!',
                                'error'
                            )
                        });
                } else if (
                    /* Read more about handling dismissals below */
                    result.dismiss === Swal.DismissReason.cancel
                ) {
                    swalWithBootstrapButtons.fire(
                        'Cancelled',
                        'Your imaginary file is safe :)',
                        'error'
                    )
                }
            })
        }

        function invite(url) {
            axios.post(url)
                .then(function (response) {
                    if (response.data.status == 200) {
                        Swal.fire(
                            'Good job!',
                            response.data.message,
                            response.data.type,
                        )
                    } else {
                        Swal.fire(
                            'Good job!',
                            response.data.message,
                            response.data.type,
                        )
                    }
                })
                .catch(function (error) {
                    Swal.fire(
                        'Good job!',
                        'Something went wrong!',
                        'error'
                    )
                });
        }

        function accepts(url, id){
            axios.post(url)
                .then(function (response) {
                    if (response.data.status == 200) {
                        $('#' + id).remove();
                    } else {
                        // Swal.fire(
                        //     'Good job!',
                        //     response.data.message,
                        //     response.data.type,
                        // )
                    }
                })
                .catch(function (error) {
                    Swal.fire(
                        'Good job!',
                        'Something went wrong!',
                        'error'
                    )
                });
        }
    </script>
@endsection

<x-app-layout>
    <x-slot name="header">
        {{--        <h2 class="font-semibold text-xl text-gray-800 leading-tight">--}}
        {{--            {{ __('Dashboard') }}--}}
        {{--        </h2>--}}
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl grid grid-cols-4 gap-16 mx-auto sm:px-6 lg:px-8">
            <div class="bg-white col-span-2 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    Friend List
                </div>
                <table class="border-separate border border-green-800">
                    <tbody>
                    @foreach($friendList as $friend)
                        <tr id="{{$friend->id}}">
                            <td class="border border-green-600">{{$friend->first_name.' '.$friend->last_name}}</td>
                            <td class="border border-green-600">
                                <a href="javascript:void(0)"
                                   onclick="deleted('{{route('friends.remove',$friend->id)}}',{{$friend->id}})"
                                   class="btn btn-danger btn-sm">Unfriend</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="bg-white col-span-1 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
                <table class="border-separate border border-green-800">
                    <tbody>
                    @foreach($friendRequestList as $friendRequest)
                        <tr id="{{$friendRequest->id}}">
                            <td class="border border-green-600">{{$friendRequest->first_name.' '.$friendRequest->last_name}}</td>
                            <td class="border border-green-600">
                                <a href="javascript:void(0)"
                                   onclick="accepts('{{route('friends.accept',$friendRequest->id)}}',{{$friendRequest->id}})"
                                   class="btn btn-danger btn-sm">Accept</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="bg-white col-span-1 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    You're logged in!
                </div>
                <table class="border-separate border border-green-800">
                    <tbody>
                    @foreach($userList as $user)
                        <tr id="{{$user->id}}">
                            <td class="border border-green-600">{{$user->first_name.' '.$user->last_name}}</td>
                            <td class="border border-green-600">
                                <a href="javascript:void(0)"
                                   onclick="invite('{{route('friends.request',$user->id)}}')"
                                   class="btn btn-danger btn-sm">Invite Friend</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>

